server 'whyjustify.com', :app, :web, :primary => true

set :user,        ""
set :application, ""
set :domain,      ""
set :repository,  "git@github.com:websoft/traxpacking.git"
set :deploy_to,   "/var/XYZ"
set :shared_path, "#{deploy_to}/shared"
set :remote_cached_repo,  "#{shared_path}/cached-copy"
set :current_dir, "/var/XYZ/www.xyz.com"
set :use_sudo, false
set :keep_releases, 5

set :db_name, ""
set :db_user, ""
set :db_password, ""
set :email_response, ""

after("deploy:symlink", "deploy:restart")

namespace :deploy do
