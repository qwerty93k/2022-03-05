@extends('layouts.app')

@section('content')

<div class="container">

<!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createArticleModal">
        New Article
    </button>
        

{{-- Atvaizdavimas --}}
    
    <div class="alert alert-success d-none" id="alert"></div> {{--Alert pranesimas--}}
    <table id="article-table" class="table table-striped">
        <tr>
            <th>Id</th>
            <th>Article Title</th>
            <th>Description</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
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
    </table>

    <table class="template">
        <tr>
            <td class="col-article-id"></td>
            <td class="col-article-title"></td>
            <td class="col-article-description"></td>
            <td class="col-article-type"></td>
            <td>
                <button class="btn btn-danger delete-article" type="submit" data-articleid="{{$article->id}}">Delete</button>
                <button type="button" class="btn btn-primary show-article" data-bs-toggle="modal" data-bs-target="#showArticleModal" data-articleid="{{$article->id}}">Show</button>
                <button type="button" class="btn btn-secondary edit-article" data-bs-toggle="modal" data-bs-target="#editArticleModal" data-articleid="{{$article->id}}">Edit</button>
            </td>
        </tr>
    </table>
</div>

<script>

    $.ajaxSetup({ //formos apsaugos imitavimas csrf
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function(){

        //2budas
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

        function createRowFormHtml(articleId, articleTitle, articleDescription, type_id){
            $(".template tr").addClass("article"+articleId);
            $(".template .delete-article").attr('data-articleid', articleId);
            $(".template .show-article").attr('data-articleid', articleId);
            $(".template .edit-article").attr('data-articleid', articleId);
            $(".template .col-article-id").html('data-articleid', articleId);
            $(".template .col-article-title").html('data-articleid', articleId);
            $(".template .col-article-description").html('data-articleid', articleId);
            $(".template .col-article-type").html('data-articleid', articleId);

            //console.log($(".template tbody".html());
            return $(".template tbody").html();
        }

        createRowFormHtml(5);

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
                data: {article_title: article_title, article_description: article_description, type_id: type_id}, // duomenys
                success: function(data){ // tikrina ar uzklausa pasieke serveri ir spausdina pranesima
                    let html;
                    
                    //1variantas
                    // html += "<tr class='article"+data.articleId+"'>";
                    // html += "<td>"+data.articleId+"</td>"
                    // html += "<td>"+data.articleTitle+"</td>"
                    // html += "<td>"+data.articleDescription+"</td>"
                    // html += "<td>"+data.type_id+"</td>"
                    // html += "<td>";
                    // html += "<button class='btn btn-danger delete-article' type='submit' data-articleid='"+data.articleId+"'>Delete</button>";
                    // html += "<button type='button' class='btn btn-primary show-article' data-bs-toggle='modal' data-bs-target='#showArticleModal' data-articleid='{{$article->id}}'>Show</button>";
                    // html += "<button type='button' class='btn btn-secondary edit-article' data-bs-toggle='modal' data-bs-target='#editArticleModal' data-articleid='{{$article->id}}'>Edit</button></td>";
                    // html += "</td>"
                    // html += "</tr>";

                   // html = createRow(data.articleId, data.articleTitle, data.articleDescription, data.typeId);

                    html = createRowFormHtml(data.articleId, data.articleTitle, data.articleDescription, data.type_id);
                    $("#article-table").append(html);

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
        })
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
        })

    })
</script>

@endsection