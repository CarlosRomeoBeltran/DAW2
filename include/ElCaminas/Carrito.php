<?php
namespace ElCaminas;
use \PDO;
use \ElCaminas\Producto;
class Carrito
{
    protected $connect;
    /** Sin parámetros. Sólo crea la variable de sesión
    */
    public function __construct()
    {
        global $connect;
        $this->connect = $connect;
        if (!isset($_SESSION['carrito'])){
            $_SESSION['carrito'] = array();
        }

    }
    public function addItem($id, $cantidad){
        $_SESSION['carrito'][$id] = $cantidad;
    }
    public function deleteItem($id){
      unset($_SESSION['carrito'][$id]);
    }
    public function empty(){
      unset($_SESSION['carrito']);
      self::__construct();
    }
    public function howMany(){
      return count($_SESSION['carrito']);
    }

    public function getTotal(){  /*creado nuevo*/
      $totalCarrito = 0;
      foreach($_SESSION['carrito'] as $key => $cantidad){
        $producto = new Producto($key);
        $totalCarrito += $producto->getPrecioReal() * $cantidad;
      }
      return $totalCarrito;
    }

    public function toHtml(){
      $redirect = "/tienda2/";  /*Cambiar por tu carpeta en el localhost*/
      if(isset($_GET["redirect"])){
      	$redirect = $_GET["redirect"];
      }

      $str = <<<heredoc
      <table class="table">
        <thead> <tr> <th></th> <th>Producto</th> <th>Cantidad</th> <th>Precio</th> </tr></thead>
        <tbody>
heredoc;
      if ($this->howMany() > 0){
        $i = 0;
        foreach($_SESSION['carrito'] as $key => $cantidad){
          $producto = new Producto($key);
          $i++;
                                 /*He eliminado el subtotal de la tabla, dato redundante*/
          $str .=  "<tr><th scope='row'>$i</th><td><a href='" .  $producto->getUrl() . "'>" . $producto->getNombre() . " </a>";
          $str .= "<a class='open-modal' title='Haga clic para ver el detalle del producto'       href='" .  $producto->getUrl() . "'>";
          $str .=     "<span style='color:#000' class='fa fa-external-link'></span>";
          $str .= "</a></td><td>$cantidad</td><td>" .  $producto->getPrecioReal() ." €</td><td><a href='./carro.php?action=delete&id=" .$producto -> getId() ."' onclick='return checkDelete();'><span class='glyphicon glyphicon-remove'></span></a></td>";
        }
      }
      $str .= <<<heredoc
          </tr>
        </tbody>
      </table>
heredoc;
      $str.="<div class='row'>
              <p class='pull-right' style='margin-right:200px'><strong>Total:  </strong> ". $this->getTotal() ." €</p><div class='pull-right' id='paypal-button-container'></div>
              <a href='".$redirect."' class='btn btn-primary' style='margin-left: 15px'>Seguir comprando</a>
              <a href='#' class='btn btn-warning' style=''>Checkout</a></div>";
      return $str;
    }
}
?>
