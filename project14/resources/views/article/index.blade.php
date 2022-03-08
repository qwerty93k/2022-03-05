@extends('layouts.app')

@section('content')

<div class="container">

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createArticleModal">
            New Article
        </button>
        
        <!-- Create Modal -->
        <div class="modal fade" id="createArticleModal" tabindex="-1" aria-labelledby="createArticleLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createArticleLabel">Article Create</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{--AjaxForm--}}
                        <div class="ajaxForm">
                            <div class="form-control">
                                <label for="article_title">Title</label>
                                <input type="text" name="article_title" id="article_title" class="form-control">
                            </div>
                            <div class="form-control">
                                <label for="article_description">Description</label>
                                <input type="text" name="article_description" id="article_description" class="form-control">
                            </div>
                            <div class="form-control">
                                <label for="type_id">Type</label>
                                <select name="type_id" id="type_id" class="form-control">
                                    @foreach ($types as $title)
                                    <option value={{$title->id}}>{{$title->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="submit-ajax-form">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{-- Show Modal --}}
        <div class="modal fade" id="showArticleModal" tabindex="-1" aria-labelledby="showArticleLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showArticleLabel">Article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <div class="show-article-id"></div>
                            <div class="show-article-name"></div>
                            <div class="show-article-description"></div>
                            <div class="show-article-type"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

{{-- Edit Modal --}}

        <div class="modal fade" id="editArticleModal" tabindex="-1" aria-labelledby="editArticleLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editArticleLabel">Edit Article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="ajaxForm">
                            <input type="hidden" id="edit_article_id" name="article_id" />
                            <div class="form-control">
                                <label for="article_title">Title</label>
                                <input type="text" name="article_title" id="edit_article_title" class="form-control">
                            </div>
                            <div class="form-control">
                                <label for="article_description">Description</label>
                                <input type="text" name="article_description" id="edit_article_description" class="form-control">
                            </div>
                            <div class="form-control">
                                <label for="type_id">Type</label>
                                <select name="type_id" id="edit_type_id" class="form-control">
                                    @foreach ($types as $title)
                                    <option value={{$title->id}}>{{$title->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="update-article">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
</div>

<script>

    $.ajaxSetup({ //formos apsaugos imitavimas csrf
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function(){

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
                    let html = "<tr class='article"+data.articleId+"'><td>"+data.articleId+"</td><td>"+data.articleTitle+"</td><td>"+data.articleDescription+"</td><td>"+data.type_id+"</td><td><button class='btn btn-danger delete-article' type='submit' data-articleid='"+data.articleId+"'>Delete</button><button type='button' class='btn btn-primary show-article' data-bs-toggle='modal' data-bs-target='#showArticleModal' data-articleid='{{$article->id}}'>Show</button><button type='button' class='btn btn-secondary edit-article' data-bs-toggle='modal' data-bs-target='#editArticleModal' data-articleid='{{$article->id}}'>Edit</button></td></tr>";
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