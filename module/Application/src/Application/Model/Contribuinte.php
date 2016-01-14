<?php
  namespace Application\Model;
   
  use Doctrine\ORM\Mapping as ORM;
   
  /**
   * @ORM\Entity
   */
  class Contribuinte {
   
      /**
       * @ORM\Id
       * @ORM\GeneratedValue("AUTO")
       * @ORM\Column(type="integer")
       */
      private $id;
      /**
       * @ORM\Column(type="string", length=50)
       */
      private $login;
      /**
       * @ORM\Column(type="string", length=15)
       */
      private $senha;
      /**
       * @ORM\Column(type="decimal")
       */
      private $num_cnpj;
      
      public function getId() {
          return $this->id;
      }
   
      public function setId($id) {
          $this->id = $id;
      }
   
      public function getLogin() {
          return $this->login;
      }
   
      public function setLogin($login) {
          $this->login = $login;
      }
   
      public function getSenha() {
          return $this->senha;
      }
   
      public function setSenha($senha) {
          $this->senha = $senha;
      }
   
      public function getNum_cnpj() {
          return $this->num_cnpj;
      }
   
      public function setNum_cnpj($num_cnpj) {
          $this->num_cnpj = $num_cnpj;
      }
      
  }
