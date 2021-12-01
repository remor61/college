<?php

function dbConnection()
{
    $con = mysqli_connect("localhost", "root", "", "biblioteca");

    if (!$con) {
        echo "<h2>Erro na conexao com a base dados...</h2>";
        echo "<h2> Erro " . mysqli_connect_errno() . ".</h2>";
        die();
    }
    $con->set_charset("utf8");
    return $con;
}

function mostraTabela($qtdeColunas, $consulta, $func)
{

    if ($consulta->num_rows > 0) {
        $i = 0;
        $tab = "";
        while ($row = mysqli_fetch_array($consulta, MYSQLI_NUM)) {
            $tab .= "<tr valign = center>";
            for ($j = 0; $j < $qtdeColunas; $j++) {
                $tab .= "<td width = 180 height = 6>" . htmlspecialchars($row[$j]) . "&nbsp;</td>";
            }
            $tab .= "<td></td>";
            $tab .= "</tr>";
            $i++;
        }
        $tab .= "<p></p>";
    } else {
        $tab = "<p>Nenhum livro encontrado com estes parâmetros de busca.</p>";
    }
    echo $tab;
}

function getAuthors(){

    $con = dbConnection();
    $result = mysqli_query($con, "SELECT ID,NOME FROM AUTHOR");
    $idResult = array();
    $nameResult = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $idResult[] = $row[0];
        $nameResult[] = $row[1];
    }

    $finalRes = array('id' => $idResult, 'name' => $nameResult);

    echo json_encode($finalRes);
    $con->close();
}

function getYears(){
    $con = dbConnection();
    $result = mysqli_query($con, "SELECT DISTINCT(PUBLICATION_YEAR) FROM BOOK");
    $yearResult = array();

    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $yearResult[] = $row[0];
    }

    echo json_encode($yearResult);
    $con->close();
}

function getBooks(){
    $con = dbConnection();
    $result = mysqli_query($con, "SELECT ID, NAME FROM BOOK");
    $idResult = array();
    $bookResult = array(); 

    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $idResult[] = $row[0];
        $bookResult[] = $row[1];
    }

    $finalRes = array('id' => $idResult, 'book' => $bookResult);

    echo json_encode($finalRes);
    $con->close();
}

function deleteBook(){
    $con = dbConnection();
    $book = intval($_REQUEST['book']);

    $collection = mysqli_query($con, "SELECT TOTAL_QUANTITY,AVAILABLE_QUANTITY FROM COLLECTION WHERE BOOK = $book");
    $row = mysqli_fetch_array($collection,MYSQLI_NUM);

    $total_quantity = $row[0];
    $available_quantity = $row[1];

     if($total_quantity>1 && $available_quantity > 0){
         mysqli_query($con, "UPDATE COLLECTION SET TOTAL_QUANTITY = $total_quantity-1,AVAILABLE_QUANTITY = $available_quantity-1 WHERE BOOK = $book");
     }
     else if($total_quantity>1 && available_quantity == 0){
         mysqli_query($con, "UPDATE COLLECTION SET TOTAL_QUANTITY = $total_quantity-1 WHERE BOOK = $book");
     }
     else if($total_quantity == 1){
         $delete = mysqli_query($con, "DELETE FROM BOOK_AUTHOR WHERE BOOK = $book");
         if($delete){
             $delete = mysqli_query($con, "DELETE FROM BOOK_PUBLISHER WHERE BOOK = $book");
         } 
         if($delete){
             $delete = mysqli_query($con, "DELETE FROM COLLECTION WHERE BOOK = $book");
         }
         if($delete){
             mysqli_query($con, "DELETE FROM BOOK WHERE ID = $book");
         }
     }
}

function addBookQty(){
    $con = dbConnection();
    $book = intval($_REQUEST['book']);

    $collection = mysqli_query($con, "SELECT TOTAL_QUANTITY,AVAILABLE_QUANTITY FROM COLLECTION WHERE BOOK = $book");
    $row = mysqli_fetch_array($collection,MYSQLI_NUM);

    $total_quantity = $row[0];
    $available_quantity = $row[1];

    mysqli_query($con, "UPDATE COLLECTION SET TOTAL_QUANTITY = $total_quantity+1, AVAILABLE_QUANTITY = $available_quantity+1 WHERE BOOK = $book");
}

function addNewBook(){
    $con = dbConnection();
    $book = $con->real_escape_string($_REQUEST['book']);
    $author = intval($_REQUEST['author']);
    $year = intval($_REQUEST['year']);
    $publisher = intval($_REQUEST['publisher']);
    $quantity = intval($_REQUEST['quantity']);

    //add into BOOK
    $inserting = mysqli_query($con, "INSERT INTO `BOOK` (`ID`,`PUBLICATION_YEAR`,`NAME`) VALUES (null,$year,'$book')");
   
    //get ID from new entry in BOOK
    $added_book =  mysqli_query($con, "SELECT ID FROM BOOK WHERE NAME = '$book' AND PUBLICATION_YEAR = $year");
    $row = mysqli_fetch_array($added_book,MYSQLI_NUM);
    $book_id = $row[0];

    if($inserting){
        mysqli_query($con, "INSERT INTO `COLLECTION` (`ID`,`BOOK`,`TOTAL_QUANTITY`,`AVAILABLE_QUANTITY`) VALUES (null,$book_id,$quantity,$quantity)");
        mysqli_query($con, "INSERT INTO `BOOK_AUTHOR` (`ID`,`BOOK`,`AUTHOR`) VALUES (null,$book_id, $author)");    
        mysqli_query($con, "INSERT INTO `BOOK_PUBLISHER` (`ID`, `BOOK`, `PUBLISHER`) VALUES (null,$book_id,$publisher)");
    }
}

function searchBooks(){
    $con = dbConnection();
    $book = $con->real_escape_string($_REQUEST['book']);
    $author = intval($_REQUEST['author']);
    $publication_year = intval($_REQUEST['publication_year']);

    //filters

    //book name
    if($author==-1 && $publication_year==-1 && $book != ''){
        $result = mysqli_query($con, 
        "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
            FROM BOOK
                INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
                INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
                INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
                INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
                INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
            WHERE BOOK.NAME LIKE '%$book%'
            ORDER BY AUTHOR"
        );
    }
    //author
    else if($author>-1 && $publication_year==-1 && $book == ''){
        $result = mysqli_query($con, 
        "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
            FROM BOOK
                INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
                INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
                INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
                INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
                INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
            WHERE BOOK_AUTHOR.AUTHOR = $author
            ORDER BY AUTHOR"
        );
    }
    //year
    else if($author==-1 && $publication_year>-1 && $book == ''){
        $result = mysqli_query($con, 
        "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
            FROM BOOK
                INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
                INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
                INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
                INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
                INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
            WHERE BOOK.PUBLICATION_YEAR LIKE $publication_year
            ORDER BY AUTHOR"
        );
    }
    //book name and author
    else if($author>-1 && $publication_year==-1 && $book != ''){
        $result = mysqli_query($con, 
        "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
            FROM BOOK
                INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
                INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
                INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
                INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
                INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
            WHERE BOOK.NAME LIKE '%$book%'
                AND BOOK_AUTHOR.AUTHOR = $author
            ORDER BY AUTHOR"
        );
    }
    //book name and year
    else if($author==-1 && $publication_year>-1 && $book != ''){
        $result = mysqli_query($con, 
        "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
            FROM BOOK
                INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
                INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
                INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
                INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
                INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
            WHERE BOOK.NAME LIKE '%$book%'
                AND BOOK.PUBLICATION_YEAR LIKE $publication_year
            ORDER BY AUTHOR"
        );
    }
    //author and year
    else if($author>-1 && $publication_year>-1 && $book == ''){
        $result = mysqli_query($con, 
        "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
            FROM BOOK
                INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
                INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
                INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
                INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
                INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
            WHERE BOOK.PUBLICATION_YEAR LIKE $publication_year
                AND BOOK_AUTHOR.AUTHOR = $author
            ORDER BY AUTHOR"
        );
    }
    //book name, author and year
    else if($book != '' && $author>-1 && $publication_year>-1){
        $result = mysqli_query($con, "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
        FROM BOOK
            INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
            INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
            INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
            INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
            INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID 
        WHERE BOOK.NAME LIKE '%$book%'
            AND BOOK_AUTHOR.AUTHOR = $author
            AND BOOK.PUBLICATION_YEAR = $publication_year
        ORDER BY AUTHOR");
    }
    //sem filtros
    else{
        $result = mysqli_query($con, "SELECT BOOK.NAME as BOOK, AUTHOR.NOME as AUTHOR, BOOK.PUBLICATION_YEAR as YEAR, PUBLISHER.NAME as PUBLISHER, COLLECTION.AVAILABLE_QUANTITY as QUANTITY
        FROM BOOK
            INNER JOIN BOOK_AUTHOR ON BOOK_AUTHOR.BOOK = BOOK.ID
            INNER JOIN BOOK_PUBLISHER ON BOOK_PUBLISHER.BOOK = BOOK.ID
            INNER JOIN AUTHOR ON AUTHOR.ID = BOOK_AUTHOR.AUTHOR
            INNER JOIN PUBLISHER ON PUBLISHER.ID = BOOK_PUBLISHER.PUBLISHER
            INNER JOIN COLLECTION ON COLLECTION.BOOK = BOOK.ID");
    }

    mostraTabela(5, $result, "Book");

    $con->close();
}

function GetAuthorsAndPublishers(){

    $con = dbConnection();
    $authors = mysqli_query($con, "SELECT ID,NOME FROM AUTHOR");
    $idAuthors = array();
    $nameAuthors = array();

    while ($row = mysqli_fetch_array($authors, MYSQLI_NUM)) {
        $idAuthors[] = $row[0];
        $nameAuthors[] = $row[1];
    }

    $publishers = mysqli_query($con, "SELECT ID,NAME FROM PUBLISHER");
    
    $idPublishers = array();
    $namePublishers = array();

    while ($row = mysqli_fetch_array($publishers, MYSQLI_NUM)) {
        $idPublishers[] = $row[0];
        $namePublishers[] = $row[1];
    }

    $finalRes = array('idAuthors' => $idAuthors, 'nameAuthors' => $nameAuthors, 'idPublishers' => $idPublishers, 'namePublishers' => $namePublishers);

    echo json_encode($finalRes);
    $con->close();
}

function addUser(){
    $con = dbConnection();
    $username = $con->real_escape_string($_REQUEST['username']);
    $address = $con->real_escape_string($_REQUEST['address']);
    $cpf = $con->real_escape_string($_REQUEST['cpf']);
    $birthdate = $con->real_escape_string($_REQUEST['birthdate']);

    $inserting = mysqli_query($con, "INSERT INTO `USUARIO` (`ID`,`NAME`,`ADDRESS`,`CPF`,`BIRTH_DATE`) VALUES (null,'$username','$address','$cpf',STR_TO_DATE('$birthdate','%Y-%m-%d'))");
}

if (@$_REQUEST['action'] == "getAuthors") {
    getAuthors();
}
if (@$_REQUEST['action'] == "getYears") {
    getYears();
}
if (@$_REQUEST['action'] == "getBooks") {
    getBooks();
}
if (@$_REQUEST['action'] == "deleteBook") {
    deleteBook();
}
if (@$_REQUEST['action'] == "addBookQty") {
    addBookQty();
}
if (@$_REQUEST['action'] == "GetAuthorsAndPublishers") {
    GetAuthorsAndPublishers();
}
if (@$_REQUEST['action'] == "addNewBook") {
    addNewBook();
}
if (@$_REQUEST['action'] == "searchBooks") {
    searchBooks();
}
if (@$_REQUEST['action'] == "addUser") {
    addUser();
}