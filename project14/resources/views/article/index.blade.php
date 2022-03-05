@extends('layouts.app')

@section('content')


<div class="container">

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
            <div class="form-control">
                <button id="subit-ajax-form" type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>

{{-- Forma --}}

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

            article_title = $('#article_name').val();
            article_description = $('#article_description').val();
            type_id = $('#type_id').val();

            $.ajax({ // siuncia ajax uzklausa i serveri
                type: 'POST', //method
                url: '{{route("article.storeAjax")}}', // action
                data: {article_title: article_title, article_description: article_description, type_id: type_id} // duomenys
            }); 

            console.log(article_title + " " + article_description + " " +  type_id);
        });
    })
</script>

@endsection