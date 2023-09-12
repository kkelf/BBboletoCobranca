<?php
namespace BBboletoCobranca\NovoBoletoPHP;

use Dompdf\Dompdf;
use chillerlan\QRCode\QRCode;
use BBboletoCobranca\NovoBoletoPHP\FormatterHelper;

class BoletoFactory {
    private $twig;
    public $layouts;
    public $imageUrl;
    public $config;

    const BANCO_DO_BRASIL = 1;

    public function __construct(array $config = array())
    {
        $this->config = $config;

        $basePath = __DIR__;
        $templatePath = $basePath . '/templates';
        $this->imageUrl = array_key_exists('imageUrl', $config) ?
                          $config['imageUrl'] :
                          dirname(__DIR__, 1).'/NovoBoletoPHP/images';
        $cachePath = array_key_exists('cachePath', $config) ?
                     $config['cachePath'] :
                     $basePath . '/cache';

        $loader = new \Twig\Loader\FilesystemLoader($templatePath);

        $this->twig = new \Twig\Environment($loader, array(
            'cache' => $cachePath,
        ));

        $this->layouts = array(
            self::BANCO_DO_BRASIL => 'BBboletoCobranca\NovoBoletoPHP\BancoDoBrasil\Boleto'
        );

        $this->configure();
    }

    public function configure()
    {
        $this->twig->addFilter(new \Twig\TwigFilter('logoBase64', array($this, 'toBase64'), array(
            'is_safe' => array('html')
        )));
        $this->twig->addFilter(new \Twig\TwigFilter('qrCode', array($this, 'makeQrCode'), array(
            'is_safe' => array('html')
        )));
        $this->twig->addFilter(new \Twig\TwigFilter('imageUrl', array($this, 'makeImageUrl'), array(
            'is_safe' => array('html')
        )));
        $this->twig->addFilter(new \Twig\TwigFilter('barcode', array($this, 'barcode'), array(
            'is_safe' => array('html')
        )));
    }

    public function makeBoleto($layout, $data)
    {
        $clazz = $this->layouts[$layout];
        $boleto = new $clazz($this->twig, $data);
        return $boleto;
    }

    public function makeBoletoAsHTML($banco, $data)
    {
        return $this->makeBoleto($banco, $data)->asHTML();
    }

    public function makeBoletoAsPDF($banco, $data)
    {
        $html = $this->makeBoletoAsHTML($banco, $data);
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        return $dompdf->output();
    }

    public function toBase64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function makeQrCode($string)
    {
        if ($string) {
            return (new QRCode)->render($string);
        }
    }

    public function makeImageUrl($imageName)
    {
        $path = '';
        if (strstr($imageName, '://') || strpos('/', $imageName) === 0) {
            $path = $imageName;
        } else {
            $path = $this->imageUrl . '/' . $imageName;    
        }

        return $this->toBase64($path);
    }

    public function barcode($valor)
    {
        ob_start();

        $fino = 1 ;
        $largo = 3 ;
        $altura = 50 ;

        $barcodes[0] = "00110" ;
        $barcodes[1] = "10001" ;
        $barcodes[2] = "01001" ;
        $barcodes[3] = "11000" ;
        $barcodes[4] = "00101" ;
        $barcodes[5] = "10100" ;
        $barcodes[6] = "01100" ;
        $barcodes[7] = "00011" ;
        $barcodes[8] = "10010" ;
        $barcodes[9] = "01010" ;

        for ($f1=9; $f1>=0; $f1--) {
            for ($f2=9; $f2>=0; $f2--) {
                $f = ($f1 * 10) + $f2 ;
                $texto = "" ;

                for ($i=1; $i<6; $i++) {
                    $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2], ($i-1), 1);
                }

                $barcodes[$f] = $texto;
            }
        }

        ?>
        <img src=<?php echo $this->makeImageUrl('p.png'); ?> width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src=<?php echo $this->makeImageUrl('b.png'); ?> width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src=<?php echo $this->makeImageUrl('p.png'); ?> width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        src=<?php echo $this->makeImageUrl('b.png'); ?> width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
        <?php
        $texto = $valor;

        if ((strlen($texto) % 2) <> 0) {
            $texto = "0" . $texto;
        }

        // Draw dos dados
        while (strlen($texto) > 0) {
          $i = round(FormatterHelper::esquerda($texto,2));
          $texto = FormatterHelper::direita($texto,strlen($texto)-2);
          $f = $barcodes[$i];
          for($i=1;$i<11;$i+=2){
            if (substr($f,($i-1),1) == "0") {
              $f1 = $fino ;
            }else{
              $f1 = $largo ;
            }
        ?>
            src=<?php echo $this->makeImageUrl('p.png'); ?> width=<?php echo $f1?> height=<?php echo $altura?> border=0><img 
        <?php
            if (substr($f,$i,1) == "0") {
              $f2 = $fino ;
            }else{
              $f2 = $largo ;
            }
        ?>
            src=<?php echo $this->makeImageUrl('b.png'); ?> width=<?php echo $f2?> height=<?php echo $altura?> border=0><img 
        <?php
          }
        }

    // Draw guarda final
    ?>
    src=<?php echo $this->makeImageUrl('p.png'); ?> width=<?php echo $largo?> height=<?php echo $altura?> border=0><img 
    src=<?php echo $this->makeImageUrl('b.png'); ?> width=<?php echo $fino?> height=<?php echo $altura?> border=0><img 
    src=<?php echo $this->makeImageUrl('p.png'); ?> width=<?php echo 1?> height=<?php echo $altura?> border=0> 
      <?php
      
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
}
