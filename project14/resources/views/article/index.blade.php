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

        console.log("Jquery veikia");
        $("#submit-ajax-form").click(function(){
            let article_title;
            let article_description;
            let type_id;

            article_title = $('#article_title').val();
            article_description = $('#article_description').val();
            type_id = $('#type_id').val();

            $.ajax({ // siuncia ajax uzklausa i serveri
                type: 'POST', //method
                url: '{{route("article.storeAjax")}}', // action
                data: {article_title: article_title, article_description: article_description, type_id: type_id, sort:sort, direction:direction}, // duomenys
                success: function(data){ // tikrina ar uzklausa pasieke serveri ir spausdina pranesima
                    let html;

                    html = createRowFormHtml(article.Id, article.Title, article.Description, article.type_id);
                    $("#article-table tbody").append(html);

                    $("#createArticleModal").hide(); //isjungia modal kai prideta sekmingai
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css({overflow:'auto'}) //kad veiktu scroll po issaugojimo
                    
                    $("#alert").removeClass("d-none");
                    $("#alert").html(data.successMsg + " " + data.articleTitle);
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
                    $('#edit_type_id').val(data.typeId);
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
                    html = createRowFormHtml(article.Id, article.Title, article.Description, article.type_id);
                    //console.log(html);
                    $("#article-table tbody").append(html);
                  });
                }
            });
        });

        // SEARCH MYGTUKAS
            $('#submitSearch').click(function() {

        let searchValue = $('#searchValue').val();
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