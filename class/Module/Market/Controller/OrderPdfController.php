<?php
/**
 * Выводит заказ в PDF для печати
 * @author Ermin Nikolay <keltanas@gmail.com>
 * @link http://siteforever.ru
 */
namespace Module\Market\Controller;

use Sfcms_Controller;
use Module\Market\Object\Order;
use numeric;

class OrderPdfController extends Sfcms_Controller
{
    function indexAction()
    {
        throw new \Sfcms\Exception('Old method');
        $this->request->setTitle('Распечатать заказ');
        $this->request->setContent('Распечатать заказ');

        $order_id = $this->request->get('order_id', FILTER_VALIDATE_INT);
        $order_model = $this->getModel('Order');

        /** @var $order Order */
        $order = $order_model->find( $order_id );

        if ( $this->user->id != $order->user_id ) {
            return $this->redirect('order');
        }


        $order['summa_nds'] = 0;

        $rows = $order->Positions;

        foreach ( $rows as $row )
        {
            $order['summa_nds'] = $row['price'] * $row['count'];
        }

        $order['nds'] = round( $order['summa_nds'] * 0.18 / 1.18, 2 );

        //printVar($rows);
        $this->outputPdf( $order, $rows );
        die();
    }

    /**
     * Вывести PDF
     * @param array $account
     * @param array $rows
     * @deprecated
     * @return void
     */
    function outputPdf( array $order, array $rows )
    {
        // подключаем библиотеку
        //require_once('tcpdf/config/lang/rus.php');
        require_once('tcpdf/tcpdf.php');

        // верстка
        $fontPDF = 10;

        $firm = $this->app()->getConfig('firm');

        $user = $this->app()->getAuth()->currentUser()->attributes;

        $account = array();

        // ---------------------------------------------------------


        $pdf = new TCPDF();

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Счет');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        //set margins
        $pdf->SetMargins(15, 10, 0);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 0);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        //$pdf->setLanguageArray($l);

        $pdf->setFont('Myfont', '', 9);
        $pdf->AddPage('P');

        $x = 0;
        $y = 0;

        $pdf->SetFontSize(9);
        //$pdf->SetXY( $x, $y );

        $pdf->Cell( 20, 5, "Поставщик");
        $pdf->SetFont('', 'B');
        $pdf->Cell( 30, 5, $firm['name'], 0, 1 );

        $pdf->SetFont('', 'N');
        $pdf->Cell( 9, 5, "ИНН");
        $pdf->SetFont('', 'B');
        $pdf->Cell( 30, 5, $firm['inn']);
        $pdf->SetFont('', 'N');
        $pdf->Cell( 9, 5, "КПП");
        $pdf->SetFont('', 'B');
        $pdf->Cell( 30, 5, $firm['kpp'], 0, 1 );

        $pdf->Cell( 100, 5, $firm['address'], 0, 1 );

        $pdf->SetFont('', 'N');
        $pdf->Cell( 9, 5, "Тел.");
        $pdf->SetFont('', 'B');
        $pdf->Cell( 30, 5, $firm['phone']);
        $pdf->SetFont('', 'N');
        $pdf->Cell( 20, 5, "Тел. склада");
        $pdf->SetFont('', 'B');
        $pdf->Cell( 30, 5, $firm['stockphone'], 0, 1 );

        $pdf->Cell( 30, 5, "", 0, 1);
        $pdf->Cell( 100, 5, "Образец заполнения платёжного поручения", 0, 1 );
        $pdf->SetFont('', 'N');

        $y = $pdf->GetY();
        $x = $pdf->GetX();
        //$x_max = 118;
        //$y_max = 33;
        $x_max = 150;
        $y_max = 33;
        $pdf->Line( $x, $y, $x + $x_max, $y);
        $pdf->Line( $x, $y + $y_max/2, $x + $x_max, $y + $y_max/2);
        $pdf->Line( $x, $y + $y_max/2+5, $x + 64, $y + $y_max/2+5);
        $pdf->Line( $x, $y + $y_max, $x + $x_max, $y + $y_max);

        $pdf->Line( $x, $y, $x, $y + $y_max);

        $pdf->Line( $x+35, $y+$y_max/2, $x+35, $y+$y_max/2+5);

        $pdf->Line( $x+64, $y, $x+64, $y + $y_max);
        $pdf->Line( $x+75, $y, $x+75, $y + $y_max);
        $pdf->Line( $x+$x_max, $y, $x+$x_max, $y + $y_max);

        $pdf->SetFontSize(9);
        $pdf->MultiCell( 64, 3, $firm['bank']['name'], 0, "L", 0, 0, $x, $y );
        $pdf->MultiCell( 64, 3, "<b>Банк получателя</b>", 0, "L", 0, 0, $x, $y+11.5, 0, 1, true );

        $pdf->MultiCell( 35, 3, "<b>ИНН</b> {$firm['inn']}", 0, "L", 0, 0, $x, $y+16.5, 0, 1, true );
        $pdf->MultiCell( 28, 3, "<b>КПП</b> {$firm['kpp']}", 0, "L", 0, 0, $x+35, $y+16.5, 0, 1, true );

        $pdf->MultiCell( 64, 3, "{$firm['name']}", 0, "L", 0, 1, $x, $y+21, 1, 1, true );
        $pdf->SetFont('', 'B');
        $pdf->MultiCell( 28, 3, "Получатель", 0, "L", 0, 0, $x, $y+27.5, 0, 1, true );
        $pdf->SetFont('', 'N');

        $pdf->MultiCell( 10.5, 3, "БИК\nСч.№", 0, "L", 0, 0, $x+64, $y );
        //$pdf->SetFontSize(9);
        $pdf->MultiCell( 42, 3, $firm['bank']['bik']."\n".$firm['bank']['ks'], 0, "L", 0, 0, $x+75, $y );

        //$pdf->SetFontSize(7);
        $pdf->MultiCell( 10.5, 3, "Сч.№", 0, "L", 0, 0, $x+64, $y+16.5 );
        //$pdf->SetFontSize(9);
        $pdf->MultiCell( 42, 3, $firm['nch'], 0, "L", 0, 0, $x+75, $y+16.5 );

        $pdf->SetFontSize(7);

        $pdf->SetXY( $x, $y + $y_max+2 );

        $view_nds = number_format( round( $order['nds'], 2 ), 2, '.', '' );
        list( $rub, $kop ) = explode( '.', $view_nds );

        $date = date('d.m.Y',$order['date']);

        $pdf->Cell(150, 4, "Назначение платежа: Оплата по счету № {$order['id']} от ".date('d.m.Y',$order['date']).
                           " за товар, в т.ч. НДС $rub руб. $kop коп.", 0, true);
        //$pdf->Cell(150, 1, "", 0, true);


        $pdf->SetFont('', 'B', 14);

        $doc_name = $this->request->get('contract', FILTER_VALIDATE_INT) === false ? "СЧЁТ" : "ДОГОВОР-СЧЁТ";

        $pdf->Cell(190, 8, $doc_name." № И-{$order['id']} от {$date}", 0, true, "C");
        $pdf->SetFont('', 'N', 8);
        $pdf->Cell(20, 4, "Покупатель");
        $pdf->SetFont('', 'N');
        $user_name  = ( $user['name'] ? $user['name'] : $user['fname'].' '.$user['lname'] );
        $user_name  = $user_name ? $user_name : $user['login'];
        $pdf->Cell(100, 4, $user_name, 0, 1);
        $pdf->SetFont('', 'B');

        if ( !empty($user['inn']) && !empty($user['kpp']) ) {
            $pdf->Cell(9, 4, "ИНН");
            $pdf->SetFont('', 'B');
            $pdf->Cell(20, 4, $user['inn']);
            $pdf->SetFont('', 'N');
            $pdf->Cell(9, 4, "КПП");
            $pdf->SetFont('', 'B');
            $pdf->Cell(20, 4, $user['kpp'], 0, 1);
        }

        if ( $user['phone'] ) {
            $pdf->SetFont('', 'N');
            $pdf->Cell(9, 4, "Телефон ");
            $pdf->SetFont('', 'B');
            $pdf->Cell(9, 5, $user['phone'], 0, 1);
        }

        //$pdf->Cell(9, 4, "", 0, 1);

        // шапка таблицы

        $pdf->setFont('', 'B', 9.5);
        $pdf->multiCell(11, 15, "№\nп/п", 1, 'C', 0, 0, '', '', true, 3, false, true, 0);
        $pdf->multiCell(49, 15, "\nАртикул", 1, 'C', 0, 0);
        $pdf->multiCell(43, 15, "\nНаименование", 1, 'C', 0, 0);
        $pdf->multiCell(18, 15, "Кол-во\nшт.", 1, 'C', 0, 0);
        $pdf->multiCell(12, 15, "Ед.\nизм.", 1, 'C', 0, 0);
        $pdf->multiCell(20, 15, "Цена\nбез НДС\nруб.", 1, 'C', 0, 0, '', '', true, 3);
        $pdf->multiCell(24, 15, "Сумма\nбез НДС\nруб.", 1, 'C');

        //
        //      Р Я Д Ы
        //

        $i = 0;

        $summ_nds   = 0;
        $summ       = 0;
        $nds        = 0;
        $page       = 1;

        define('FIRST_PAGE', 20);
        define('NEXT_PAGE', 35);

        //$pdf->Output();
        //die();

        $rows_count = count( $rows );
        if ( $rows_count )
        {
            foreach( $rows as $g )
            {
                $i++;

                $g['summa_nds'] = $g['count'] * $g['price'];
                $g['summa']     = round( $g['summa_nds'] / 1.18, 2);
                $g['nds']       = $g['summa_nds'] - $g['summa'];

                $summ += $g['summa'];
                $nds  += $g['nds'];
                $summ_nds  += $g['summa_nds'];

                //$pdf->SetCellPadding(0);
                $pdf->SetAutoPageBreak( false, 0 );
                $pdf->setFont('', '', 9);
                $pdf->Cell(11, 6, $i, 1, 0, 'L', 0, 0, 1);
                $pdf->Cell(49, 6, $g['articul'], 1, 0, 'L', 0, 0, 1);
                $pdf->Cell(43, 6, $g['name'], 1, 0, 'L', 0, 0, 1);
                $pdf->Cell(18, 6, $g['count'], 1, 0, 'R', 0, 0, 1);
                $pdf->Cell(12, 6, $g['item'], 1, 0, 'L', 0, 0, 1);
                $pdf->Cell(20, 6, number_format( $g['price'], 2, ',', ' '), 1, 0, 'R', 0, 0, 1);
                $pdf->Cell(24, 6, number_format( $g['summa'], 2, ',', ' '), 1, 1, 'R', 0, 0, 1);
                $pdf->SetAutoPageBreak( true, 1 );
                //$pdf->SetCellPadding(1);

                if (  /*( $i % FIRST_PAGE == 0 && $page == 1 )
                       || ( ( $i - FIRST_PAGE ) % NEXT_PAGE == 0 && $page > 1 )
                       ||*/ ( $pdf->GetY() > 130 && $i == $rows_count - 1 ) ||
                            ( $pdf->GetY() > 250 )
                ) {

                    $pdf->AddPage('P');
                    $page ++;

                    $pdf->setFont('', 'B', 9.5);
                    $pdf->multiCell(11, 15, "№\nп/п", 1, 'C', 0, 0, '', '', true, 3, false, true, 0);
                    $pdf->multiCell(49, 15, "\nНаименование", 1, 'C', 0, 0);
                    $pdf->multiCell(43, 15, "\nОписание", 1, 'C', 0, 0);
                    $pdf->multiCell(18, 15, "Кол-во\nшт.", 1, 'C', 0, 0);
                    $pdf->multiCell(12, 15, "Ед.\nизм.", 1, 'C', 0, 0);
                    $pdf->multiCell(20, 15, "Цена\nбез НДС\nруб.", 1, 'C', 0, 0, '', '', true, 3);
                    $pdf->multiCell(24, 15, "Сумма\nбез НДС\nруб.", 1, 'C');

                }
            }
        }
        //$summ = round( $summ, 2 );
        //$summ_nds = round( $summ * 1.18, 2 );
        //$nds = round( $summ * 0.18, 2 );

        $pdf->Cell(60, 6, "    Сумма без НДС:", "TL", 0);
        $pdf->Cell(117, 6, number_format( $summ, 2, ',', ' ' ).' руб.', "TR", 1, "R");

        $pdf->Cell(60, 6, "    Сумма НДС (18 %):", "LB", 0);
        $pdf->Cell(117, 6, number_format( $nds, 2, ',', ' ' ).' руб.', "RB", 1, "R");

        $pdf->SetFont('', 'B');
        $pdf->Cell(60, 6, "    Итого к оплате:", "BL", 0);
        $pdf->Cell(117, 6, number_format( $summ_nds, 2, ',', ' ' ).' руб.', "RB", 1, "R");
        $pdf->SetFont('', 'N');


        list( $n, $k ) = explode( '.', number_format( $summ_nds, 2, '.', '' ) );

        $numeric    = new numeric();//$numeric->write( $n )
        $ruble      = array(1 => 'рубль',   2 => 'рубля',   5 => 'рублей');
        $kop        = array(1 => 'копейка', 2 => 'копейки', 5 => 'копеек');

        $pdf->SetFont('', 'B');
        $pdf->Cell( 170, 6,
                    "Итого к оплате: ".
                    $numeric->write( intval( $n ) )." ".
                    $ruble[ $numeric->num_125( $n ) ]." ".
                    substr( '00'.round( $k ), -2)." ".
                    $kop[ $numeric->num_125( $k ) ], 0, 1, "L");

        //$pdf->SetFont('', 'N', 7);
        //$pdf->Cell( 170, 4, "Срок поставки ориентировочно 2 - 3 недели.", 0, 1, 'L' );

        //$this->write(8, cp2utf("Итого к оплате: Шесть тысяч пять рублей 33 копейки\n"));
        if ( !$this->request->get( 'contract', FILTER_VALIDATE_INT ) )
        {
            $pdf->SetFont('', '', 7);
            $pdf->write(4, "Счет действителен в течении 5 банковских дней. Датой платежа считается дата поступления денежных средств на расчетный счет Продавца.\n");
        }


        /*
         *  /// ПЕЧАТЬ ПОРЯДКА ОПЛАТЫ И ПОЛУЧЕНИЯ ПРОДУКЦИИ
         */

        $x = $pdf->GetX();
        $y = $pdf->GetY();

//        $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/files/account/stamp.png', $x, $y, 40, 40);
//        $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/files/account/signature.png', $x + 80, $y+8, 20, 20);
//        $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/files/account/signature.png', $x + 85, $y+17, 20, 20);

        $pdf->SetFont('', '', 8);
        $pdf->SetXY( $x + 40, $y + 17 );
        $pdf->Cell( 80, 4, "Генеральный директор" );
        $pdf->Cell( 40, 4, $firm['gendir'] );
        $pdf->SetXY( $x + 40, $y + 25 );
        $pdf->Cell( 80, 4, "Главный бухгалтер" );
        $pdf->Cell( 40, 4, $firm['buh'] );

        $pdf->SetY( $y + 40 );

        $pdf->SetFontSize( 6 );
        if ( isset( $firm['contact'] ) ) {
            $pdf->write(4, "Контактное лицо - {$firm['contact']}\n");
        }

        $number = str_replace('/', '-', $order['id']);
        $pdf->Output();
        die();
    }
    
}
