<div class="col-lg-9 order-is-first">
    <div class="row">
        {if(empty($products))}
            <div class="empty-element">Пусто</div>
        {else}
            {for($l = 0; $l < count($products); $l++)}
                <div class="col-lg-6">
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
                            <a class="btn btn-primary buy-btn" href="../shop/product?id={{$products[$l]->id}}">
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
                                    <h4 class="modal-title">Описание</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
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

<div class="col-lg-3 order-is-last">
    {categories}
	
      	{if(is_auth())}
            {include file="/home/navigation.tpl"}
			 <div id="case_banner">
             <script>get_case_banner();</script>
             </div>
	    {else}
		    {include file="/index/authorization.tpl"}
            {include file="/home/navigation.tpl"}
	    {/if}
</div>