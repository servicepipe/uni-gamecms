    <div class="r_block">
        <div class="r_block_head">Товар</div>
        <div class="r_block_c">
            <div class="row">
                {if(empty($products))}
                    <div class="empty-element">Пусто</div>
                {else}
                    {for($l = 0; $l < count($products); $l++)}
                        <div class="col-xs-6">
                            <div class="block rcon-shop-product">
                                <div class="image" style="background-image: url('{{$products[$l]->image}}')">
                                    <span class="price btn btn-danger">{{$products[$l]->price}}</span>
                                    <span class="title"
                                          title="{{$products[$l]->title}}"
                                          tooltip="yes"
                                    >
                                        {{$products[$l]->title}}
                                    </span>
                                </div>

                                <div class="actions">
                                    <a class="btn btn-default buy-btn" href="../shop/product?id={{$products[$l]->id}}">
                                        Купить
                                    </a>
                                    <a class="btn btn-default description-btn"
                                       data-toggle="modal"
                                       data-target="#description{{$products[$l]->id}}"
                                    >
                                        Описание
                                    </a>
                                </div>
                            </div>

                            <div class="modal fade" id="description{{$products[$l]->id}}">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">
                                                Описание

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body with_code">
                                            {{$products[$l]->description}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/for}
                {/if}
            </div>
        </div>
    </div>
</div>

<div class="left_block">
    {categories}

    {if(is_auth())}
        {include file="/home/right_col.tpl"}
    {else}
        {include file="/index/reg_form.tpl"}
    {/if}
</div>