@extends('layouts.app')

@section('content')

<style>
    th div {
      cursor: pointer;
    }
</style> 

<div class="container">

<!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createArticleModal">
        New Article
    </button>

    <input id="hidden-sort" type="hidden" value="id"/>
    <input id="hidden-direction" type="hidden" value="asc"/>

    <div id="alert" class="alert alert-success d-none">
    </div>  

{{-- paieska --}}
    <div class="searchAjaxForm">
        <input id="searchValue" type="text">
        <button type="button" id="submitSearch">Find</button>
    </div>

{{-- Atvaizdavimas --}}
    
    <div class="alert alert-success d-none" id="alert"></div> {{--Alert pranesimas--}}
    <table id="article-table" class="table table-striped">
        <thead>
            <tr>
                <th><div class="article-sort" data-sort="id" data-direction="asc">Id</div></th>
                <th><div class="article-sort" data-sort="title" data-direction="asc">Article Title</div></th>
                <th><div class="article-sort" data-sort="description" data-direction="asc">Description</div></th>
                <th><div class="article-sort" data-sort="articleType->title" data-direction="asc">Type</div></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article) 
                <tr class="article{{$article->id}}">
                    <td class="col-article-id">{{$article->id}}</td>
                    <td class="col-article-title">{{$article->title}}</td>
                    <td class="col-article-description">{{$article->description}}</td>
                    <td class="col-article-type">{{$article->articleType->title}}</td>
                    <td>
                        <button class="btn btn-danger delete-article" type="submit" data-articleid="{{$article->id}}">Delete</button>
                        <button type="button" class="btn btn-primary show-article" data-bs-toggle="modal" data-bs-target="#showArticleModal" data-articleid="{{$article->id}}">Show</button>
                        <button type="button" class="btn btn-secondary edit-article" data-bs-toggle="modal" data-bs-target="#editArticleModal" data-articleid="{{$article->id}}">Edit</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<table class="template d-none">
    <tr>
      <td class="col-article-id"></td>
      <td class="col-article-title"></td>
      <td class="col-article-description"></td>
      <td class="ccol-article-type"></td>
      <td>
        <button class="btn btn-danger delete-article" type="submit" data-articleid="">DELETE</button>
        <button type="button" class="btn btn-primary show-article" data-bs-toggle="modal" data-bs-target="#showArticleModal" data-articleid="">Show</button>
        <button type="button" class="btn btn-secondary edit-article" data-bs-toggle="modal" data-bs-target="#editArticleModal" data-articleid="">Edit</button>
      </td>
    </tr>  
</table> 

<script>

    $.ajaxSetup({ //formos apsaugos imitavimas csrf
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function(){

        function createRow(articleId, articleTitle, articleDescription, typeId){
            let html;
            html += "<tr class='article"+data.articleId+"'>";
            html += "<td>"+articleId+"</td>"
            html += "<td>"+articleTitle+"</td>"
            html += "<td>"+articleDescription+"</td>"
            html += "<td>"+type_id+"</td>"
            html += "<td>";
            html += "<button class='btn btn-danger delete-article' type='submit' data-articleid='"+articleId+"'>Delete</button>";
            html += "<button type='button' class='btn btn-primary show-article' data-bs-toggle='modal' data-bs-target='#showArticleModal' data-articleid='{{$article->id}}'>Show</button>";
            html += "<button type='button' class='btn btn-secondary edit-article' data-bs-toggle='modal' data-bs-target='#editArticleModal' data-articleid='{{$article->id}}'>Edit</button></td>";
            html += "</td>"
            html += "</tr>";

            return html;
        }

        function createRowFromHtml(articleId, articleTitle, articleDescription, type_id) {
          $(".template tr").removeAttr("class");
          $(".template tr").addClass("article"+articleId);
          $(".template .delete-article").attr('data-articleid', articleId );
          $(".template .show-article").attr('data-articleid', articleId );
          $(".template .edit-article").attr('data-articleid', articleId );
          $(".template .col-article-id").html(articleId );
          $(".template .col-article-title").html(articleTitle );
          $(".template .col-article-description").html(articleDescription );
          $(".template .col-article-type").html(type_id );
    
          return $(".template tbody").html();
        }

        console.log("Jquery veikia");
        $("#submit-ajax-form").click(function(){
            let article_title;
            let article_description;
            let type_id;
            let sort;
            let direction;

            article_title = $('#article_title').val();
            article_description = $('#article_description').val();
            type_id = $('#type_id').val();
            sort = $('#hidden-sort').val();
            direction = $('#hidden-direction').val();

            $.ajax({ // siuncia ajax uzklausa i serveri
                type: 'POST', //method
                url: '{{route("article.storeAjax")}}', // action
                data: {article_title: article_title, article_description: article_description, type_id: type_id, sort:sort, direction:direction}, // duomenys
                success: function(data){ // tikrina ar uzklausa pasieke serveri ir spausdina pranesima
                    let html;

                    if($.isEmptyObject(data.errorMessage)){
                        $("#article-table tbody").html('');
                        $.each(data.article, function(key. artcile){
                            html = createRowFromHtml(article.Id, article.Title, article.Description, article.type_id);
                            $("#article-table tbody").append(html);
                        });

                    $("#createArticleModal").hide(); //isjungia modal kai prideta sekmingai
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css({overflow:'auto'}) //kad veiktu scroll po issaugojimo
                    
                    $("#alert").removeClass("d-none");
                    $("#alert").html(data.successMsg + " " + data.articleTitle);

                    $('#article_title').val('');
                    $('#article_description').val('');
                    $('#type_id').val('');
                    } else {
                        console.log(data.errorMessage);
                        console.log(data.errors);
                        $('.create-input').removeClass('is-invalid');
                        $('.invalid-feedback').html('');

                        $.each(data.errors, function(key, error){
                            console.log(key);//key = input id
                            $('#'+key).addClass('is-invalid');
                            $('.input_'+key).html("<strong>"+error+"</strong>");
                        });
                    }
                }
            }); 
        });
        

        // delete mygtukas

        $(document).on('click','.delete-article', function() {
            let articleid;
            articleid = $(this).attr('data-articleid'); //pasirenka nuspausta mygtuka
            console.log(articleid);

            $.ajax({ // siuncia ajax uzklausa i serveri
                type: 'POST', //method
                url: '/article/deleteAjax/' + articleid, // action
                success: function(data){
                    $('.article' + articleid).remove();
                    $("#alert").removeClass("d-none");
                    $("#alert").html(data.successMsg);
                }
            }); 
        })

        // show mygtukas

        $(document).on('click','.show-article', function() {
            let articleid;
            articleid = $(this).attr('data-articleid'); //pasirenka nuspausta mygtuka
            console.log(articleid);

            $.ajax({
                type: 'GET',
                url: '/article/showAjax/' + articleid,
                success: function(data){
                    $('.show-article-id').html(data.articleId); // html reiksme is json masyvo
                    $('.show-article-title').html(data.articleTitle);
                    $('.show-article-description').html(data.articleDescription);
                    $('.show-article-type').html(data.typeId);
                }
            }); 
        })

        // edit mygtukas

            $(document).on('click','.edit-article', function() {
            let articleid;
            articleid = $(this).attr('data-articleid'); //pasirenka nuspausta mygtuka
            console.log(articleid);

            $.ajax({
                type: 'GET',
                url: '/article/showAjax/' + articleid,
                success: function(data){
                    $('#edit_article_id').val(data.articleId);
                    $('#edit_article_title').val(data.articleTitle); //input = val // div = html
                    $('#edit_article_description').val(data.articleDescription);

                    $('#edit_type_id option').removeAttr('selected');
                    $('#edit_type_id').val(data.articleTypeId);
                    $('#edit_type_id .type'+ data.articleTypeId).attr("selected", "selected"); 
                }
            }); 
        });
        $(document).on('click','.update-article', function(){
            let articleid;
            let article_title;
            let article_description;
            let type_id;

            articleid = $('#edit_article_id').val();
            article_title = $('#edit_article_title').val();
            article_description = $('#edit_article_description').val();
            type_id = $('#edit_type_id').val();

            $.ajax({
                type: 'POST',
                url: '/article/updateAjax/' + articleid,
                data: {article_title: article_title, article_description: article_description, type_id: type_id},
                success: function(data){
                    $(".article"+articleid+ " " + ".col-article-title").html(data.articleTitle)
                    $(".article"+articleid+ " " + ".col-article-description").html(data.articleDescription)
                    $(".article"+articleid+ " " + ".col-article-type").html(data.typeId)
                    
                    $("#alert").removeClass("d-none");
                    $("#alert").html(data.successMsg);

                    $("#editArticleModal").hide();
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css({overflow:'auto'})
                }
            }); 
        });

        // SORT MYGTUKAS

        $('.article-sort').click(function() {
          let sort;
          let direction;

          sort = $(this).attr('data-sort');
          direction = $(this).attr('data-direction');

          $("#hidden-sort").val(sort);
          $("#hidden-direction").val(direction);

          if(direction == 'asc'){
            $(this).attr('data-direction', 'desc')
          } else {
            $(this).attr('data-direction', 'asc')
          }

          $.ajax({
                type: 'GET',// formoje method POST GET
                url: '{{route("article.indexAjax")}}'  ,// formoje action
                data: {sort: sort, direction: direction },
                success: function(data) {
                  console.log(data.article);
                  //perbraizysiu lentele
                    //ciklo kuris eina per visa masyva
                    //kiekvienos ciklo iteracijos metu mes tiesiog turime klienta prikabinti prie tbody tago
                  //mygtuku rikiavimui
                  // foreach 
                  $("#article-table tbody").html('');
                  $.each(data.article, function(key,article){ //jquery foreach ciklas
                    let html;
                    html = createRowFromHtml(article.Id, article.Title, article.Description, article.type_id.title);
                    //console.log(html);
                    $("#article-table tbody").append(html);
                  });
                }
            });
        });

        // SEARCH MYGTUKAS
        $(document).on('input', '#searchValue', function() {
        let searchValue = $('#searchValue').val();
        let searchFieldCount= searchValue.length;

            if(searchFieldCount == 0) {
                console.log("Field is empty");
                $(".search-feedback").css('display', 'block');
                $(".search-feedback").html("Field is empty");
            }else if (searchFieldCount != 0 && searchFieldCount< 3 ) {
                console.log("Min 3");
                $(".search-feedback").css('display', 'block');
                $(".search-feedback").html("Min 3");
            }else {
                $(".search-feedback").css('display', 'none');
            console.log(searchFieldCount);
            console.log(searchValue);

        $.ajax({
                type: 'GET',
                url: '{{route("article.searchAjax")}}'  ,
                data: {searchValue: searchValue},
                success: function(data) {
                    
                  if($.isEmptyObject(data.errorMessage)) {
                    //sekmes atvejis
                    $("#article-table").show();
                    $("#alert").addClass("d-none");
                    $("#article-table tbody").html('');
                     $.each(data.article, function(key, article) {
                          let html;
                          html = createRowFromHtml(article.Id, article.Title, article.Description, article.type_id);
                          // console.log(html)
                          $("#article-table tbody").append(html);
                     });                             
                  } else {
                        $("#article-table").hide();
                        $('#alert').removeClass('alert-success');
                        $('#alert').addClass('alert-danger');
                        $("#alert").removeClass("d-none");
                        $("#alert").html(data.errorMessage); 
                  }                            
                }
            });
        });

    })
</script>

@endsection