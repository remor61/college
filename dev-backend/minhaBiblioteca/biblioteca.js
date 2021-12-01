function ajaxCall(stringCall, callback){
    var httpRequest = new XMLHttpRequest;
        
        httpRequest.onreadystatechange = function(){
            if (httpRequest.readyState === 4) {
                if (httpRequest.status === 200) {
                  callback(httpRequest.responseText);
                }
            }
        };
        httpRequest.open('GET', stringCall);
        httpRequest.send();
}

function start(){
    ajaxCall("biblioteca.php?action=getAuthors",getAuthors);
    ajaxCall("biblioteca.php?action=getYears",getYears);
}

function getAuthors(authorsList){

    let authors = document.getElementById("author_id");
    let jsonData = JSON.parse(authorsList);

    for(i=0;i<jsonData.name.length;i++){

        let option = document.createElement("option");
		option.text = jsonData.name[i];
		option.value = jsonData.id[i];
		authors.add(option);
    }
}

function getYears(yearsList){
    let years = document.getElementById("publication_year_id");
    let jsonData = JSON.parse(yearsList);

    for(i=0;i<jsonData.length;i++){

        let option = document.createElement("option");
		option.text = jsonData[i];
		option.value = jsonData[i];
		years.add(option);
    }
}

function searchBooks(){
    let book = document.getElementById("book_id").value;
    let author = document.getElementById("author_id").value;
    let publication_year = document.getElementById("publication_year_id").value;

    document.getElementById("book_id").value = '';
    document.getElementById("author_id").value = -1;
    document.getElementById("publication_year_id").value = -1;


    let params = "&book="+book+"&author="+author+"&publication_year="+publication_year;
    console.log(params);
    ajaxCall("biblioteca.php?action=searchBooks"+params,bookList);
}

function bookList(books){   
    document.getElementById('table_books').removeAttribute("hidden");
    document.getElementById('book_table_id').innerHTML = books;
}

//deleting a book
function showDeleteForm(){
    ajaxCall("biblioteca.php?action=getBooks",getBooks);
}

function getBooks(booksList){
    document.getElementById('form_delete').hidden = false;

    let books = document.getElementById("books_select");
    let jsonData = JSON.parse(booksList);

    for(i=0;i<jsonData.book.length;i++){

        let option = document.createElement("option");
		option.text = jsonData.book[i];
		option.value = jsonData.id[i];
		books.add(option);
    }
}

function deleteBook(){
    let book = document.getElementById("books_select").value;
    ajaxCall("biblioteca.php?action=deleteBook&book="+book,afterDeletion);
}

function afterDeletion(){
    document.getElementById('form_delete').hidden = true;
    alert("Uma unidade do livro removida com sucesso!");
}

//adding a book
function showAddForm(){
    document.getElementById('form_add').hidden = false;
    document.getElementById('btn_group_add').hidden = false;    
}

function addExistingBook(){
    document.getElementById('btn_group_add').hidden = true;
    document.getElementById('add_existing_div').hidden = false;   
    
    ajaxCall("biblioteca.php?action=getBooks",getBooksAdd);
}

function getBooksAdd(booksList){

    let books = document.getElementById("books_select_add");
    let jsonData = JSON.parse(booksList);

    for(i=0;i<jsonData.book.length;i++){

        let option = document.createElement("option");
		option.text = jsonData.book[i];
		option.value = jsonData.id[i];
		books.add(option);
    }
}

function addBookQty(){
    let book = document.getElementById("books_select_add").value;
    ajaxCall("biblioteca.php?action=addBookQty&book="+book,afterInsert);
}

function addNewBook(){
    document.getElementById('btn_group_add').hidden = true;
    document.getElementById('add_new_div').hidden = false;   

    ajaxCall("biblioteca.php?action=GetAuthorsAndPublishers",getAuthorsAndPublishers);
}

function getAuthorsAndPublishers(authorsAndPublishers){

    let authors = document.getElementById("add_author_id");
    let publishers = document.getElementById("add_publisher_id");

    let jsonData = JSON.parse(authorsAndPublishers);

    for(i=0;i<jsonData.nameAuthors.length;i++){

        let option = document.createElement("option");
		option.text = jsonData.nameAuthors[i];
		option.value = jsonData.idAuthors[i];
		authors.add(option);
    }

    for(i=0;i<jsonData.namePublishers.length;i++){

        let option = document.createElement("option");
		option.text = jsonData.namePublishers[i];
		option.value = jsonData.idPublishers[i];
		publishers.add(option);
    }
}

function addBook(){
    let book = document.getElementById("add_book_id").value;
    let author = document.getElementById("add_author_id").value;
    let year = document.getElementById("add_year_id").value;
    let publisher = document.getElementById("add_publisher_id").value;
    let quantity = document.getElementById("add_qty_id").value;

    let params = "&book="+book+"&author="+author+"&year="+year+"&publisher="+publisher+"&quantity="+quantity;

    ajaxCall("biblioteca.php?action=addNewBook"+params,afterInsert);
}

function afterInsert(){
    document.getElementById('add_existing_div').hidden = true;
    document.getElementById('form_add').hidden = true;

    let select = document.getElementById("books_select_add");

    for(i=select.length-1;i>=0;i--){
        select.remove(i);
    }

    alert("Livro adicionado com sucesso!");
}

function showAddUser(){
    document.getElementById('form_add_user').hidden = false;   
}

function addUser(){
    let username = document.getElementById("add_username_id").value;
    let address = document.getElementById("add_address_id").value;
    let cpf = document.getElementById("add_cpf_id").value;
    let birthdate = document.getElementById("add_birthdate_id").value;

    let params = "&username="+username+"&address="+address+"&cpf="+cpf+"&birthdate="+birthdate;

    ajaxCall("biblioteca.php?action=addUser"+params, afterInsertUser);
}

function afterInsertUser(a){
    console.log(a);
    document.getElementById("add_username_id").value = '';
    document.getElementById("add_address_id").value = '';
    document.getElementById("add_cpf_id").value = '';
    document.getElementById("add_birthdate_id").value = '';

    document.getElementById('form_add_user').hidden = true;   

    alert("Usuário adicionado com sucesso!");
}