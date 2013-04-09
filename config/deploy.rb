# inspired by https://gist.github.com/2116910

set :scm,        :git
set :branch,     'master'
set :deploy_via, :remote_cache

set :stages, %w(production staging)
set :default_stage, "staging"
require 'capistrano/ext/multistage'

#ssh_options[:forward_agent] = true

namespace :deploy do
    before :deploy do
        unless exists?(:deploy_to)
        raise "Please invoke me like `cap stage deploy` where stage is production/staging"
        end
        
        if not defined?(@previous_revision)

        end
    end
    
    desc "Does the things needed for firt time deployment"
    task :initialize_app do
        if not remote_file_exists?("#{shared_path}/public/.htaccess")  
            puts "create things we need at first deployment"
            run "mkdir -p -m 777 #{shared_path}/data/session"
            run "mkdir -p -m 777 #{shared_path}/public/cache"
            run "mkdir -p -m 777 #{shared_path}/public/media"
            run "mkdir -p -m 777 #{shared_path}/public/upload"
            run "mkdir -p -m 777 #{shared_path}/public/compressed"
            run "mkdir -p -m 777 #{shared_path}/library"
            run "cp #{remote_cached_repo}/public/_htaccess_prod #{shared_path}/public/.htaccess"
            run "cd #{shared_path}/library && svn co -q http://framework.zend.com/svn/framework/standard/branches/release-1.11/library/Zend"
            run "cd #{shared_path}/library && svn co -q http://framework.zend.com/svn/framework/extras/branches/release-1.11/library/ZendX"
        end
    
    
        run "cd #{remote_cached_repo} && git submodule init"
        run "cd #{remote_cached_repo} && git submodule update"
    end
    
    
    desc "Restart apache."
    task :restart do
        sudo "/usr/sbin/apachectl graceful"
    end

    desc "Send a deployment notification"
    task :notify do
        if defined?(@previous_revision)
            changes = `git log --no-merges --pretty=format:"* %s %b (%cn)" #{previous_revision}.. | replace '<unknown>' ''`

            if not changes.empty?
                
                send_email email_response, {:body => "Deployed changes:\n#{changes}", :subject => "Deploying to #{stage}"}
            end
        end
        
    end


    desc "Creates a Backup before migrating with doctrine"
    task :db_backup, :roles => :web do
        set :use_statement, "echo " + %("DROP DATABASE IF EXISTS #{db_name} ; CREATE DATABASE IF NOT EXISTS  #{db_name}; USE #{db_name} ;")
        run "#{use_statement} > #{release_path}/db_before_migration_backup.sql"
        run "mysqldump --opt -u #{db_user} --password=#{db_password} #{db_name} >> #{release_path}/db_before_migration_backup.sql"
    end


    desc "Migrates the database changes."
    task :doctrine do
        changes = capture("cd #{current_release}/scripts && doctrine orm:schema-tool:update --dump-sql")
        if !(changes.include? "Nothing to update")
            puts "Pending database updates:\n#{changes}"
          
            migrate = Capistrano::CLI.ui.ask "Would you like to update the database (Y/n)? "
            if migrate.empty? || migrate != 'n'
                run "cd #{current_release}/scripts && doctrine orm:schema-tool:update --force"
            end
        end
        run "cd #{current_release}/scripts && doctrine orm:generate-proxies"
    end

    # Create create_symlinks for asset files.
    # Shared location for large asset files and content uploaded by users
    desc "Creates create_symlinks for asset files (like /media,/public/upoad or the Zend-Library)."
    task :link_shared_data, :roles => :web do
        run "ln -sf #{shared_path}/public/.htaccess #{current_release}/public/.htaccess"
        run "ln -sf #{shared_path}/data/session #{current_release}/data/session"
        run "ln -sf #{shared_path}/public/js/jquery #{current_release}/public/js/jquery"
        run "ln -sf #{shared_path}/public/cache #{current_release}/public/cache"
        run "ln -sf #{shared_path}/public/compressed #{current_release}/public/compressed"
        run "ln -sf #{shared_path}/public/media #{current_release}/public/media"
        run "ln -sf #{shared_path}/public/upload #{current_release}/public/upload"
        run "ln -sf #{shared_path}/library/Zend #{current_release}/library/Zend"
        run "ln -sf #{shared_path}/library/ZendX #{current_release}/library/ZendX"

    end

    desc "Minify and Merge general Javascript and CSS files"
    task :minify_js_and_css, :roles => :web do
        run "php #{current_release}/scripts/lib/compressor.php production"
    end
end

namespace :logs do
    desc "Tails the remote error logs."
    task :watch, :on_error => :continue do
        run "tail -f /var/log/php_errors.log" do |channel, stream, data|
            trap("INT") { puts 'Interupted'; exit 0; }
            puts # for an extra line break before the host name
            puts "#{channel[:host]}: #{data}"
            break if stream == :err
        end
    end
end

# Let's run this tasks immediately before the final create_symlink is made
after("deploy:update_code", "deploy:initialize_app")
after("deploy:update_code", "deploy:notify")
after("deploy:update_code", "deploy:link_shared_data")
after("deploy:update_code", "deploy:minify_js_and_css")
after("deploy:update_code", "deploy:db_backup")
after("deploy:update_code", "deploy:doctrine")


# Email function for deployment notifications.
require 'net/smtp'

def send_email(to, opts={})
   
    opts[:server] ||= 'localhost'
    opts[:from] ||= "deploy@#{domain}"
    opts[:from_alias] ||= 'Traxpacking Deployment'
    opts[:subject] ||= "Deployment Summary"
    opts[:body] ||= "Code has been deployed..."

    msg = <<END_OF_MESSAGE
From: #{opts[:from_alias]} <#{opts[:from]}>
To: #{to}
MIME-Version: 1.0
Content-type: text/plain
Subject: #{opts[:subject]}

#{opts[:body]}
END_OF_MESSAGE

    Net::SMTP.start(opts[:server]) do |smtp|
        smtp.send_message msg, opts[:from], to
  end
end

def remote_file_exists?(full_path)
  'true' ==  capture("if [ -e #{full_path} ]; then echo 'true'; fi").strip
end
