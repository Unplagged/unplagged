<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Uses ISO 639-1 to map languages.
 */
class Unplagged_Language{
  private $languageCodes = array(
    'aa'=>'Afar',
    'ab'=>'Abkhaz',
    'ae'=>'Avestan',
    'af'=>'Afrikaans',
    'ak'=>'Akan',
    'am'=>'Amharic',
    'an'=>'Aragonese',
    'ar'=>'Arabic',
    'as'=>'Assamese',
    'av'=>'Avaric',
    'ay'=>'Aymara',
    'az'=>'Azerbaijani',
    'ba'=>'Bashkir',
    'be'=>'Belarusian',
    'bg'=>'Bulgarian',
    'bh'=>'Bihari',
    'bi'=>'Bislama',
    'bm'=>'Bambara',
    'bn'=>'Bengali',
    'bo'=>'Tibetan',
    'br'=>'Breton',
    'bs'=>'Bosnian',
    'ca'=>'Catalan; Valencian',
    'ce'=>'Chechen',
    'ch'=>'Chamorro',
    'co'=>'Corsican',
    'cr'=>'Cree',
    'cs'=>'Czech',
    'cu'=>'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',
    'cv'=>'Chuvash',
    'cy'=>'Welsh',
    'da'=>'Danish',
    'de'=>'German',
    'dv'=>'Divehi; Dhivehi; Maldivian;',
    'dz'=>'Dzongkha',
    'ee'=>'Ewe',
    'el'=>'Greek, Modern',
    'en'=>'English',
    'eo'=>'Esperanto',
    'es'=>'Spanish; Castilian',
    'et'=>'Estonian',
    'eu'=>'Basque',
    'fa'=>'Persian',
    'ff'=>'Fula; Fulah; Pulaar; Pular',
    'fi'=>'Finnish',
    'fj'=>'Fijian',
    'fo'=>'Faroese',
    'fr'=>'French',
    'fy'=>'Western Frisian',
    'ga'=>'Irish',
    'gd'=>'Scottish Gaelic; Gaelic',
    'gl'=>'Galician',
    'gn'=>'Guaraní',
    'gu'=>'Gujarati',
    'gv'=>'Manx',
    'ha'=>'Hausa',
    'he'=>'Hebrew (modern)',
    'hi'=>'Hindi',
    'ho'=>'Hiri Motu',
    'hr'=>'Croatian',
    'ht'=>'Haitian; Haitian Creole',
    'hu'=>'Hungarian',
    'hy'=>'Armenian',
    'hz'=>'Herero',
    'ia'=>'Interlingua',
    'id'=>'Indonesian',
    'ie'=>'Interlingue',
    'ig'=>'Igbo',
    'ii'=>'Nuosu',
    'ik'=>'Inupiaq',
    'io'=>'Ido',
    'is'=>'Icelandic',
    'it'=>'Italian',
    'iu'=>'Inuktitut',
    'ja'=>'Japanese',
    'jv'=>'Javanese',
    'ka'=>'Georgian',
    'kg'=>'Kongo',
    'ki'=>'Kikuyu, Gikuyu',
    'kj'=>'Kwanyama, Kuanyama',
    'kk'=>'Kazakh',
    'kl'=>'Kalaallisut, Greenlandic',
    'km'=>'Khmer',
    'kn'=>'Kannada',
    'ko'=>'Korean',
    'kr'=>'Kanuri',
    'ks'=>'Kashmiri',
    'ku'=>'Kurdish',
    'kv'=>'Komi',
    'kw'=>'Cornish',
    'ky'=>'Kirghiz, Kyrgyz',
    'la'=>'Latin',
    'lb'=>'Luxembourgish, Letzeburgesch',
    'lg'=>'Luganda',
    'li'=>'Limburgish, Limburgan, Limburger',
    'ln'=>'Lingala',
    'lo'=>'Lao',
    'lt'=>'Lithuanian',
    'lu'=>'Luba-Katanga',
    'lv'=>'Latvian',
    'mg'=>'Malagasy',
    'mh'=>'Marshallese',
    'mi'=>'Maori',
    'mk'=>'Macedonian',
    'ml'=>'Malayalam',
    'mn'=>'Mongolian',
    'mr'=>'Marathi (Marāṭhī)',
    'ms'=>'Malay',
    'mt'=>'Maltese',
    'my'=>'Burmese',
    'na'=>'Nauru',
    'nb'=>'Norwegian Bokmål',
    'nd'=>'North Ndebele',
    'ne'=>'Nepali',
    'ng'=>'Ndonga',
    'nl'=>'Dutch',
    'nn'=>'Norwegian Nynorsk',
    'no'=>'Norwegian',
    'nr'=>'South Ndebele',
    'nv'=>'Navajo, Navaho',
    'ny'=>'Chichewa; Chewa; Nyanja',
    'oc'=>'Occitan',
    'oj'=>'Ojibwe, Ojibwa',
    'om'=>'Oromo',
    'or'=>'Oriya',
    'os'=>'Ossetian, Ossetic',
    'pa'=>'Panjabi, Punjabi',
    'pi'=>'Pali',
    'pl'=>'Polish',
    'ps'=>'Pashto, Pushto',
    'pt'=>'Portuguese',
    'qu'=>'Quechua',
    'rm'=>'Romansh',
    'rn'=>'Kirundi',
    'ro'=>'Romanian, Moldavian, Moldovan',
    'ru'=>'Russian',
    'rw'=>'Kinyarwanda',
    'sa'=>'Sanskrit',
    'sc'=>'Sardinian',
    'sd'=>'Sindhi',
    'se'=>'Northern Sami',
    'sg'=>'Sango',
    'si'=>'Sinhala, Sinhalese',
    'sk'=>'Slovak',
    'sl'=>'Slovene',
    'sm'=>'Samoan',
    'sn'=>'Shona',
    'so'=>'Somali',
    'sq'=>'Albanian',
    'sr'=>'Serbian',
    'ss'=>'Swati',
    'st'=>'Southern Sotho',
    'su'=>'Sundanese',
    'sv'=>'Swedish',
    'sw'=>'Swahili',
    'ta'=>'Tamil',
    'te'=>'Telugu',
    'tg'=>'Tajik',
    'th'=>'Thai',
    'ti'=>'Tigrinya',
    'tk'=>'Turkmen',
    'tl'=>'Tagalog',
    'tn'=>'Tswana',
    'to'=>'Tonga (Tonga Islands)',
    'tr'=>'Turkish',
    'ts'=>'Tsonga',
    'tt'=>'Tatar',
    'tw'=>'Twi',
    'ty'=>'Tahitian',
    'ug'=>'Uighur, Uyghur',
    'uk'=>'Ukrainian',
    'ur'=>'Urdu',
    'uz'=>'Uzbek',
    've'=>'Venda',
    'vi'=>'Vietnamese',
    'vo'=>'Volapük',
    'wa'=>'Walloon',
    'wo'=>'Wolof',
    'xh'=>'Xhosa',
    'yi'=>'Yiddish',
    'yo'=>'Yoruba',
    'za'=>'Zhuang, Chuang',
    'zh'=>'Chinese',
    'zu'=>'Zulu',
  );
  
  /**
   * 
   * @param string $languageCode
   */
  public function getLanguageFromCode($languageCode){    
    if(array_key_exists($languageCode, $this->languageCodes)){
      return $this->languageCodes[$languageCode];
    }
    
    return '';
  }
  
  /**
   * 
   * @param string $languageCode
   * @return boolean
   */
  public function isValidLanguageCode($languageCode){
    if(array_key_exists($languageCode, $this->languageCodes)){
      return true;
    }
    
    return false;
  }
  
  /**
   * @return array
   */
  public function getAllLanguages(){
    return $this->languageCodes;
  }
}