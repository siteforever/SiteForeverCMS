<?php


class Data_Object_AliasTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data_Object_Alias
     */
    protected $alias;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->alias    = new Data_Object_Alias(Model::getModel('Alias'),
            array(
                'id'        => 1,
                'alias'     => '/catalog/dc_dc_convertory/p6au_2412elf_peak',
                'controller'=> 'elcatalog',
                'action'    => 'index',
                'params'    => array('prodid'=>4865),
            )
        );
    }

    /**
     * @return void
     */
    public function testGetTable()
    {
        $this->assertInstanceOf( 'Data_Table_Alias', $this->alias->getTable() );
    }

    /**
     * @return void
     */
    public function testGenerateAlias()
    {
        $this->assertEquals(
            $this->alias->alias,
            '/'.$this->alias->generateAlias('Каталог')
            .'/'.$this->alias->generateAlias('DC/DC конверторы')
            .'/'.$this->alias->generateAlias('P6AU-2412ELF (PEAK)')
        );
    }

    /**
     * @return void
     */
    public function testGenerateAliasFromArray()
    {
        $this->assertEquals(
            $this->alias->alias,
            $this->alias->generateAliasFromArray(
                array(
                    'Каталог' , 'DC/DC конверторы' , 'P6AU-2412ELF (PEAK)'
                )
            )
        );
    }

}