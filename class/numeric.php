<?php
/**
 * Переводит число в словесный формат
 * @author keltanas
 */
class numeric 
{

    protected $N0 = 'ноль';
    protected $Ne0 = array(
                 0 => array('','один','два','три','четыре','пять','шесть',
                            'семь','восемь','девять','десять','одиннадцать',
                            'двенадцать','тринадцать','четырнадцать','пятнадцать',
                            'шестнадцать','семнадцать','восемнадцать','девятнадцать'),
                 1 => array('','одна','две','три','четыре','пять','шесть',
                            'семь','восемь','девять','десять','одиннадцать',
                            'двенадцать','тринадцать','четырнадцать','пятнадцать',
                            'шестнадцать','семнадцать','восемнадцать','девятнадцать')
                 );

    protected $Ne1 = array('','десять','двадцать','тридцать','сорок','пятьдесят',
                 'шестьдесят','семьдесят','восемьдесят','девяносто');

    protected $Ne2 = array('','сто','двести','триста','четыреста','пятьсот',
                 'шестьсот','семьсот','восемьсот','девятьсот');

    protected $Ne3 = array(1 => 'тысяча', 2 => 'тысячи', 5 => 'тысяч');

    protected $Ne6 = array(1 => 'миллион', 2 => 'миллиона', 5 => 'миллионов');


    /**
     * Печатает число словами
     *
     * @param integer $i
     * @param bool $female
     * @return bool
     */
    public function write($i, $female=false) {
      $i = intval( $i );
      if ( ($i<0) || ($i>=1e9) || !is_int($i) ) {
        return false; // Аргумент должен быть неотрицательным целым числом, не превышающим 1 миллион
      }
      if($i==0) {
        return $this->N0;
      }
      else {
        return preg_replace( array('/s+/','/\s$/'),
                             array(' ',''),
                             $this->num1e9($i, $female));
        return $this->num1e9($i, $female);
      }
    }

    public function num_125($n) {
      /* форма склонения слова, существительное с числительным склоняется
       одним из трех способов: 1 миллион, 2 миллиона, 5 миллионов */
      $n = intval( $n );
      $n100    = $n % 100;
      $n10     = $n % 10;

      if( ($n100 > 10) && ($n100 < 20) ) {
        return 5;
      }
      elseif( $n10 == 1) {
        return 1;
      }
      elseif( ($n10 >= 2) && ($n10 <= 4) ) {
        return 2;
      }
      else {
        return 5;
      }
    }

    protected function num1e9($i, $female) {
      if($i<1e6) {
        return $this->num1e6($i, $female);
      }
      else {
        return $this->num1000(intval($i/1e6), false) . ' ' .
          $this->Ne6[ $this->num_125(intval($i/1e6)) ] . ' ' . $this->num1e6( $i%1e6, $female );
      }
    }

    protected function num1e6($i, $female) {
      if($i<1000) {
        return $this->num1000($i, $female);
      }
      else {
        return $this->num1000( intval($i/1000), true ) . ' ' .
          $this->Ne3[ $this->num_125( intval($i/1000)) ] . ' ' . $this->num1000( $i%1000, $female );
      }
    }

    protected function num1000($i, $female) {
      if( $i<100) {
        return $this->num100($i, $female);
      }
      else {
        return $this->Ne2[intval($i/100)] . (($i%100)?(' '. $this->num100($i%100, $female)):'');
      }
    }

    protected function num100($i, $female) {
      $gender = $female?1:0;
      if ($i<20) {
        return $this->Ne0[$gender][$i];
      }
      else {
        return $this->Ne1[intval($i/10)] . (($i%10)?(' ' . $this->Ne0[$gender][$i%10]):'');
      }
    }

}


class Numeric_Sklon extends numeric 
{
    protected $Ne0 = array(
                 0 => array('','одном','двух','трёх','четырёх','пяти','шести',
                            'семи','восьми','девяти','десяти','одиннадцати',
                            'двенадцати','тринадцати','четырнадцати','пятнадцати',
                            'шестнадцати','семнадцати','восемнадцати','девятнадцати'),

                 1 => array('','одной','двух','трёх','четырёх','пяти','шести',
                            'семи','восьми','девяти','десяти','одиннадцати',
                            'двенадцати','тринадцати','четырнадцати','пятнадцати',
                            'шестнадцати','семнадцати','восемнадцати','девятнадцати')
    );

    protected $Ne1 = array('','десяти','двадцати','тридцати','сорока','пятьдесяти',
                 'шестьдесяти','семьдесяти','восемьдесяти','девяносто');

    protected $Ne2 = array('','ста','двухстах','трёхстах','четырёхстах','пятистах',
                 'шестистах','семистах','восьмистах','девятистах');

    protected $Ne3 = array(1 => 'тысяче', 2 => 'тысячях', 5 => 'тысячях');

    protected $Ne6 = array(1 => 'миллионе', 2 => 'миллионах', 5 => 'миллионах');
}