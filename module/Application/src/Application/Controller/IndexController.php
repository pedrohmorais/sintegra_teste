<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	public function retornaSintegra($cnpj,$tipo){
		$servidor = 'http://www.sintegra.es.gov.br/resultado.php';

            // Parametros da requisição
            $content = http_build_query(array(
                'num_cnpj' => $cnpj,'botao'=>'Consultar'
            ));

            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',                    
                    'header' => "Connection: close\r\n".
                                "Content-type: application/x-www-form-urlencoded\r\n".
                                "Content-Length: ".strlen($content)."\r\n",
                    'content' => $content                               
                )
            ));
            // Realize comunicação com o servidor
            $contents = file_get_contents($servidor, null, $context);   
            $contents = substr($contents,(strpos($contents, 'Cadastro atualizado') + 25))  ;
            $contents = substr($contents,0,(strpos($contents, '<b>VOLTAR</b>' )) ) ;

            $contents = strip_tags($contents, '<p><a>');   
            
            $contents = preg_replace('/\t+/', '', $contents);
            $contents = preg_replace('/\n+/', '', $contents);

            while(preg_match('/\s\s/',$contents) ) {
            	$contents = preg_replace('/\s\s/', '', $contents);
            }

            $contents = str_replace('<ahref="index.php"class="voltar">', '', $contents);
            $contents = str_replace('&nbsp;', '', $contents);

            //print_r(strpos($contents, utf8_decode('IDENTIFICAÇÃO')) );
            
            $contents = str_replace(utf8_decode('IDENTIFICAÇÃO - PESSOA JURÍDICA'), '||', $contents);
            $contents = str_replace(utf8_decode('ENDEREÇO'), '||', $contents);
            $contents = str_replace(utf8_decode('INFORMAÇÕES COMPLEMENTARES'), '||', $contents);

            //print_r($contents);die();
            $conteudoexp = explode('||', $contents);
            $dados['cad_at'] = $conteudoexp[0];
            if($contents == ''){
            	return false;
            }
            $conteudoexp[1] = str_replace(utf8_decode('Inscrição Estadual'), utf8_encode('|ie'), $conteudoexp[1]);
            $conteudoexp[1] = str_replace(utf8_decode('Razão Social'), utf8_encode('|r_social'), $conteudoexp[1]);
            $conteudoexp[1] = explode('|', $conteudoexp[1]);
            foreach ($conteudoexp[1] as $key => $value) {
            	$temp = explode(':', $value);
            	$dados['identificacao'][trim($temp[0])] = trim($temp[1]);
            }
            $conteudoexp[2] = str_replace(utf8_decode('Número'), utf8_encode('|numero'), $conteudoexp[2]);
            $conteudoexp[2] = str_replace(utf8_decode('Complemento'), utf8_encode('|Complemento'), $conteudoexp[2]);
            $conteudoexp[2] = str_replace(utf8_decode('Bairro'), utf8_encode('|Bairro'), $conteudoexp[2]);
            $conteudoexp[2] = str_replace(utf8_decode('Município'), utf8_encode('|municipio'), $conteudoexp[2]);
            $conteudoexp[2] = str_replace(utf8_decode('UF'), utf8_encode('|UF'), $conteudoexp[2]);
            $conteudoexp[2] = str_replace(utf8_decode('CEP'), utf8_encode('|CEP'), $conteudoexp[2]);
            $conteudoexp[2] = str_replace(utf8_decode('Telefone'), utf8_encode('|Telefone'), $conteudoexp[2]);
            $conteudoexp[2] = explode('|', $conteudoexp[2]);
            foreach ($conteudoexp[2] as $key => $value) {
            	$temp = explode(':', $value);
            	$dados['endereco'][trim($temp[0])] = trim($temp[1]);
            }
            $conteudoexp[3] = str_replace(utf8_decode('Atividade Econômica'), utf8_decode('atv_economica'), $conteudoexp[3]);
            $conteudoexp[3] = str_replace(utf8_decode('Data de Inicio de Atividade'), utf8_encode('|Dat_inicio'), $conteudoexp[3]);
            $conteudoexp[3] = str_replace(utf8_decode('Situação Cadastral Vigente'), utf8_encode('|Sit_vig'), $conteudoexp[3]);
            $conteudoexp[3] = str_replace(utf8_decode('Data desta Situação Cadastral'), utf8_encode('|Data_Situacao_Cadastral'), $conteudoexp[3]);
            $conteudoexp[3] = str_replace(utf8_decode('Regime de Apura&ccedil;&atilde;o'), utf8_encode('|Regime_apuracao'), $conteudoexp[3]);
            $conteudoexp[3] = str_replace(utf8_decode('Emitente de NFe desde'), utf8_encode('|Emitente_desde'), $conteudoexp[3]);
            $conteudoexp[3] = str_replace(utf8_decode('OBSERVAÇÃO'), utf8_encode(''), $conteudoexp[3]);
            $conteudoexp[3] = explode('|', $conteudoexp[3]);

            foreach ($conteudoexp[3] as $key => $value) {
            	$temp = explode(':', $value);
            	$dados['info_comp'][trim($temp[0])] = trim($temp[1]);
            }
            $dados['info_comp']['Emitente_desde'] =  str_replace(utf8_decode('Emitente de NFe desde'), utf8_encode('|Emitente_desde'),$dados['info_comp']['Emitente_desde']);
			
			if($tipo == 2){
            	return json_encode($dados);
			}
			else{
				return $dados;
			}
	}
    public function indexAction()
    {
    	$request = $this->getRequest();
    	$result = array();
    	if ($_GET) {
    		//print_r($_GET);
    		if(
    			!$_GET["login"] ||
    			!$_GET["senha"] ||
    			!$_GET["num_cnpj"] 
    		)
    		{
    			return new ViewModel(false);
    		}
    		//$this->validaLogin();
    		print_r($this->retornaSintegra(trim($_GET["num_cnpj"]),1) );
    		die();
    	}
    	try{
	    	if($request->isPost())
	    	{
	    		if(
	    			!$request->getPost("login") ||
	    			!$request->getPost("senha") ||
	    			!$request->getPost("num_cnpj") 
	    		)
	    		{
	    			return new ViewModel(false);
	    		}
	    		$login = $request->getPost("login");
	    		$senha = $request->getPost("senha");
	    		$num_cnpj = $request->getPost("num_cnpj");
	    		/*
	    		//tive problemas com o doctrine
	    		$contribuinte = new \Application\Model\Contribuinte();
                $contribuinte->setLogin($login);
                $contribuinte->getNum_cnpj($num_cnpj);
                $contribuinte->setSenha($senha);
 				
	    		die();
                $em = $this->getServiceLocator()->get("Doctrine\ORM\EntityManager");
                $em->persist($contribuinte);
                $em->flush();
				*/
	    		$result["resp"] = $this->retornaSintegra($num_cnpj,1);
	    	}
	    	else{
	    		$result["resp"] = '||||';
	    	}
	    }
	    catch(Exception $e){
	    	print_r($e);
	    }	
    	return new ViewModel($result);

    }
}
