<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Gerenciador de Estoque</h1>
<?php
  /* Conectar ao MySQL e selecionar o banco de dados */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      exit();
  }

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Garantir que a tabela PRODUCTS exista */
  VerifyProductsTable($connection, DB_DATABASE);

  /* Se o botão "Clear Table" for pressionado, limpar os dados da tabela */
  if (isset($_POST['clear_table'])) {
    ClearProductsTable($connection);
  }

  /* Se os campos estiverem preenchidos, adiciona um produto à tabela PRODUCTS */
  $product_name = htmlentities($_POST['Product_Name']);
  $price = htmlentities($_POST['Price']);
  $brand = htmlentities($_POST['Brand']);
  $in_stock = htmlentities($_POST['In_Stock']);

  if (strlen($product_name) && strlen($price) && strlen($brand) && strlen($in_stock)) {
    AddProduct($connection, $product_name, $price, $brand, $in_stock);
  }
?>

<!-- Formulário para adicionar produtos -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>Produto</td>
      <td>Preço</td>
      <td>Marca</td>
      <td>Estoque</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="Product_Name" maxlength="100" size="30" required />
      </td>
      <td>
        <input type="text" name="Price" maxlength="10" size="10" required />
      </td>
      <td>
        <input type="text" name="Brand" maxlength="50" size="30" value="Nome da Marca" required />
      </td>
      <td>
        <select name="In_Stock" required>
          <option value="1">Disponível</option>
          <option value="0">Em falta</option>
        </select>
      </td>
      <td>
        <input type="submit" value="Adicionar Produto" />
      </td>
    </tr>
  </table>
</form>

<!-- Exibir dados da tabela PRODUCTS -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>Produto</td>
    <td>Preço</td>
    <td>Marca</td>
    <td>Estoque</td>
  </tr>

<?php
  $result = mysqli_query($connection, "SELECT * FROM PRODUCTS");

  while($query_data = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>", $query_data['ID'], "</td>",
         "<td>", $query_data['Product_Name'], "</td>",
         "<td>R$", number_format($query_data['Price'], 2, ',', '.'), "</td>",
         "<td>", $query_data['Brand'], "</td>",
         "<td>", $query_data['In_Stock'] == 1 ? 'Disponível' : 'Em falta', "</td>";
    echo "</tr>";
  }

  mysqli_free_result($result);
  mysqli_close($connection);
?>

</table>

<!-- Botão para limpar os dados da tabela -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <input type="submit" name="clear_table" value="Limpar Tabela" />
</form>

</body>
</html>

<?php
/* Função para adicionar um produto */
function AddProduct($connection, $product_name, $price, $brand, $in_stock) {
   $pname = mysqli_real_escape_string($connection, $product_name);
   $pprice = mysqli_real_escape_string($connection, $price);
   $pbrand = mysqli_real_escape_string($connection, $brand);
   $pstock = intval($in_stock);

   $query = "INSERT INTO PRODUCTS (Product_Name, Price, Brand, In_Stock) VALUES ('$pname', '$pprice', '$pbrand', '$pstock');";

   if(!mysqli_query($connection, $query)) {
       echo("<p>Erro ao adicionar dados do produto.</p>");
   }
}

/* Função para verificar se a tabela PRODUCTS existe e criá-la se não existir */
function VerifyProductsTable($connection, $dbName) {
  if(!TableExists("PRODUCTS", $connection, $dbName)) {
    $query = "CREATE TABLE PRODUCTS (
        ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        Product_Name VARCHAR(100),
        Price DECIMAL(10, 2),
        Brand VARCHAR(50),
        In_Stock TINYINT(1)
      )";

    if(!mysqli_query($connection, $query)) {
      echo("<p>Erro ao criar tabela de PRODUTOS.</p>");
    }
  }
}

/* Função para limpar a tabela PRODUCTS */
function ClearProductsTable($connection) {
   $query = "TRUNCATE TABLE PRODUCTS"; // Ou DELETE FROM PRODUCTS

   if(!mysqli_query($connection, $query)) {
       echo("<p>Erro ao limpar a tabela de produtos.</p>");
   } else {
       echo("<p>Tabela de produtos limpa com sucesso.</p>");
   }
}

/* Função para verificar a existência da tabela */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>