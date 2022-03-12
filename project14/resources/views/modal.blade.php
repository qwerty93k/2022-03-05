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
                            <button type="button" class="btn btn-primary" id="submit-ajax-form">Create</button>
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
                            <button type="button" class="btn btn-primary update-article" id="update-article">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>