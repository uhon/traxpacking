<?php

namespace Tp\Entity;
/**
 * User: uhon
 * Date: 2012/04/10
 * GitHub: git@github.com:uhon/traxpacking.git
 */

class CountryTest extends \ModelTestCase
{
    private $countryList = array(
        "Greenland","Newfoundland","Canada","the United States","Mexico","Guatamala","Belize","El Salvador",
        "Honduras","Nicaragua","Costa Rica","Panama","Jamaica","Haiti","Puerto Rico","the Dominican Republic",
        "Cuba","Colombia","Venezuala","Guyana","Suriname","French Guiana","Ecuador","Peru","Brazil","Paraguay",
        "Bolivia","Chile","Argentina","Uruguay","the Falkland Islands","Ireland","the United Kingdom","Svalbard",
        "Iceland","Denmark","Norway","Sweden","Finland","Estonia","Latvia","Lithunia","Belarus","the Netherlands",
        "Belgium","Luxemburg","France","Germany","Poland","the Czech Republic","the Slovak Republic","Ukraine",
        "Switzerland","Austria","Hungary","Romania","Moldova","Italy","Sicily","Slovenia","Croatia",
        "Bosnia and Hervigovina","Serbia Montenegro","Portugal","Spain","the Balearik Islands","Corsica",
        "Sardinia","Albania","Macedonia","Bulgaria","Greece","Crete","Cyprus","Turkey","Russia","Georgia",
        "Azerbaijan","Armenia","Kazakstan","Mongolia","Turkmenistan","Uzbekistan","Kyrgyzstan","Tajikistan",
        "China","North Korea","South Korea","Taiwan","Hainan Island","Japan","Lebanon","Syria","Israel",
        "Jordan","Iraq","Kuwait","Iran","Afghanistan","Saudi Arabia","Qatar","the United Arab Emirates",
        "Yemen","Oman","Pakistan","India","Sri Lanka","Nepal","Bhutan","Bangladesh","Burma","Thailand","Laos",
        "Vietnam","Cambodia","the Phillipines","Brunei","Malaysia","Indonesia","East Timor","Papua New Guinea",
        "Morocco","Algeria","Tunisia","Libya","Egypt","Western Sahara","Mauritania","Mali","Niger","Chad",
        "Sudan","Eritrea","Senegal","Gambia","Guinea Bissau","Guinea","Burkina Faso","Sierra Leone","Liberia",
        "the Ivory Coast","Ghana","Togo","Benin","Nigeria","Cameroon","the Central African Republic","Ethiopia",
        "Djibouti","Somalia","Equatorial Guinea","Gabon","the Republic of Congo",
        "the Democratic Republic of Congo","Uganda","Kenya","Rwanda","Burundi","Tanzania","Angola","Zambia",
        "Malawi","Namibia","Botswana","Zimbabwe","Mozambique","Madagascar","South Africa","Swaziland","Lesotho",
        "Australia","Tasmania","New Caledonia","St Pierre and Miquelon","the Solomon Islands","Mauritius",
        "Tonga","Fiji","Niuie","Vanuatu","Samoa","Comoros","the Seychelles","Nauru","the Galapogos Islands",
        "Sao Tome","the Marshall Islands","Palau","Antilles","Aruba","Barbados","St Lucia","Cape Verde","Dominica",
        "Antigua and Barbuda","St Kits and Nevis","the British Virgin Islands","the Hawaiian Islands",
        "Turks and Caicos Islands","the Canary Islands","the Faroe Islands","Jan Mayen","Lakshadweep",
        "Nicobar Islands","Andaman Islands","the Bahamas","Bermuda","Trinidad and Tobago","Curacao", "Bonaire","St Lucia",
    );
	/**
	 *
	 */
	public function testCanSaveCountries()
	{
        $em = $this->doctrineContainer->getEntityManager();
        foreach($this->countryList as $name) {
            $country = new Country();
            $country->name = $name;
            $em->persist($country);
            $em->flush();

        }

	}

}

