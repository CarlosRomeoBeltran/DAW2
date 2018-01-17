<?php
  session_start();
  include("./include/funciones.php");
  $connect = connect_db();

  require './include/ElCaminas/Carrito.php';
  require './include/ElCaminas/Productos.php';
  require './include/ElCaminas/Producto.php';

  use ElCaminas\Carrito;
  use ElCaminas\Productos;
  use ElCaminas\Producto;
  $productos = new Productos();

  $query = " SELECT * FROM categorias WHERE id = :id";
	$statement = $connect->prepare($query);
	$statement->bindParam(':id', $_GET["id"], PDO::PARAM_INT);
	$statement->execute();
	$count = $statement->rowCount();
	if($count > 0){
		$categoria = $statement->fetch(PDO::FETCH_ASSOC);
	}else{
    http_response_code(404);
    exit();
  }
  $title = "Plantas el Caminàs -> " . $categoria["nombre"];


  require './include/JasonGrimes/Paginator.php';

  use JasonGrimes\Paginator;

  $currentPage = (isset($_GET["currentPage"]) ? $_GET["currentPage"] : 1);
  //En principio este parámetro no estaría en producción. Lo usamos para ir probando el paginador con distintos tamaños
  $itemsPerPage = (isset($_GET["itemsPerPage"]) ? $_GET["itemsPerPage"] : 2);

  // $query = " SELECT COUNT(*) as cuenta FROM productos WHERE id_categoria = :id";
  // $statement = $connect->prepare($query);
  // $statement->bindParam(':id', $_GET["id"], PDO::PARAM_INT);
  // $statement->execute();

  $totalItems = $productos->getCountProductosByCategoria($_GET["id"]);

  $state = "normal";
   if (isset($_GET["state"])){
     $state = $_GET["state"];
   }
   if ("normal" == $state){
     include("./include/header.php");
   }elseif("exclusive" == $state){
     /**
     Nada. Sólo lo pongo para que veáis este caso más claro
     */
   }

   if ("normal" == $state):?>
  <h2 class='subtitle' style='margin-left:0; margin-right:0;'><?php echo $categoria["nombre"];?></h2>
  <div id="data-container">
  <?php endif; ?>
   <div class="row">
     <?php
     foreach($productos->getProductosByCategoria($_GET["id"], $itemsPerPage, $currentPage) as $producto){
        echo $producto->getThumbnailHtml();
     }
     ?>
   </div>
   <div class="row">
     <?php
     $urlPattern = "./categoria.php?id=" . $_GET["id"] . "&itemsPerPage=$itemsPerPage&currentPage=(:num)";
     $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
     //echo $paginator->toHtml();
     include './include/JasonGrimes/examples/pager.phtml';
     ?>
   </div>
  <?php if ("normal" == $state):?>
  </div>
  <?php endif; ?>


  <div class="row">
    <h2 class='subtitle'><?php echo $categoria["nombre"];?></h2>
    <?php
    foreach($productos->getProductosByCategoria($_GET["id"], $itemsPerPage, $currentPage) as $producto){
       echo $producto->getThumbnailHtml();
    }
    ?>
  </div>
  <div class="row">
    <?php
    $urlPattern = "./categoria.php?id=" . $_GET["id"] . "&itemsPerPage=$itemsPerPage&currentPage=(:num)";
    $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
    //echo $paginator->toHtml();
    include './include/JasonGrimes/examples/pager.phtml';
    ?>
  </div>

  <!-- modal, está oculto-->
  <div class="modal fade" id="infoCarroProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Producto añadido correctamente a su carrito</h4>
      </div>
      <div class="modal-body">
        <div id='data-container'>
          <div class="row">
            <div class="col-md-3">
                <img class='img-thumbnail img-responsive' style='max-width:128px' src=''>
            </div>
            <div class="col-md-9">
                <h4 class='title'></h4>
                <p class='desc'></p>
                <input type='number' min='1' id='cantidad' value=1><button class='update' class='btn'>Actualizar</button>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4" >
              <a href="#" class="btn btn-primary">Ver carrito</a>
            </div>
            <div class="col-md-4 col-md-offset-4">
                <div class='pull-right'>
                  <b>Total carrito: <span class='total'></span> €</b>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <!-- modal, está oculto/-->
<?php
if ("normal" == $state){
  $bottomScripts = array();
  $bottomScripts[] = "loadCategorias.js";
  $bottomScripts[] = "loadCategoriaspost.js";
  $bottomScripts[] = "modalDomProducto.js";
  include("./include/footer.php");
}
?>
