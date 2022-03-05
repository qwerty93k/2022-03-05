@extends('layouts.app')

@section('content')


<div class="container">

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            New Article
        </button>
        
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Article Create</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{--AjaxForm--}}
                        <div class="alert alert-success d-none" id="alert"></div> {{--Alert pranesimas--}}
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
        
{{-- Atvaizdavimas --}}

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
                <td class="col-client-id">{{$article->id}}</td>
                <td class="col-client-title">{{$article->title}}</td>
                <td class="col-client-description">{{$article->description}}</td>
                <td class="col-client-type">{{$article->articleType->title}}</td>
                <td></td>
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
                    let html = "<tr><td>"+data.articleId+"</td><td>"+data.articleTitle+"</td><td>"+data.articleDescription+"</td><td>"+data.typeId+"</td></tr>";
                    $("#article-table").append(html);
                    
                    $("#alert").removeClass("d-none");
                    $("#alert").html(data.successMsg + " " + data.articleTitle);
                }
            }); 
        });
    })
</script>

@endsection