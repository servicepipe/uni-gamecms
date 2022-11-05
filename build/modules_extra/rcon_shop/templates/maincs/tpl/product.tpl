    <div class="r_block rcon-shop-product-in-detail">
        <div class="r_block_head">Покупка «{title}»</div>
        <div class="r_block_c">
            <a href="../{image}" class="image" data-lightbox="1">
                <img src="../{image}">
            </a>

            <br>

            <div class="with_code">
                {description}
            </div>

            {if(!empty($tarifs))}
                <div {if('{isHasTarifs}' == '2')} style="display: none" {/if}>
                    <br>
                    <label for="tarif"><b>Выберите тариф</b></label>

                    <div class="row" data-toggle="buttons">
                        {for($l = 0; $l < count($tarifs); $l++)}
                            <div class="btn-group-toggle mb-3 col-md-{if(count($params) % 4 == 0)}3{else}{if(count($params) % 3 == 0)}4{else}{if(count($params) % 2 == 0)}6{else}4{/if}{/if}{/if}">
                                <label
                                        class="btn btn-default {if($l == 0)} active {/if} btn-block"
                                        onclick="setRconShopProductTarif({{$tarifs[$l]->id}}, '{{$tarifs[$l]->price}}', '{{$tarifs[$l]->title}}')"
                                >
                                    <input name="tarifs" type="radio" class="disp-n">
                                    {{$tarifs[$l]->price}} - {{$tarifs[$l]->title}}
                                </label>
                            </div>
                        {/for}
                    </div>

                    <input type="hidden" id="tarif">
                    <input type="hidden" id="product" value="{id}">
                </div>
            {/if}

            <br>

            {if(!empty($params))}
                <label for="params"><b>Заполните поля</b></label>

                <form class="row" id="params">
                    {for($l = 0; $l < count($params); $l++)}
                        <div class="col-md-{if(count($params) % 4 == 0)}3{else}{if(count($params) % 3 == 0)}4{else}{if(count($params) % 2 == 0)}6{else}12{/if}{/if}{/if}">
                            <input type="text" class="form-control" maxlength="128" name="{{$params[$l]->slug}}" id="{{$params[$l]->slug}}" placeholder="Введите «{{$params[$l]->title}}»">
                        </div>
                    {/for}
                </form>

                <br>
            {/if}

            {if(is_auth())}
                <div class="mt-10 bs-callout bs-callout-info transition_h_2">
                    <p class="mb-0">К оплате <b id="product-price">0</b> - Вы оплачиваете <b>«{title}{if('{isHasTarifs}' == '1')} - <span id="product-title"></span>{/if}»</b> на сервере <b>«{serverName}»</b></p>
                </div>

                <div class="form-check mt-10">
                    <input class="form-check-input" id="buy-checkbox" data-status="2" type="checkbox" onclick="onRconShopBuying();">
                    <label class="form-check-label" for="buy-checkbox">
                        Я ознакомлен с <a target="_blank" href="../pages/rules">правилами</a> проекта и согласен с ними
                    </label>
                </div>

                <div id="error-result" class="bs-callout bs-callout-danger mt-10" style="display: none"></div>

                <button id="buy-btn" class="btn2 disabled mt-2">Оплатить</button>

                <div id="success-result" class="bs-callout bs-callout-success mt-10" style="display: none">
                    <p>Успешно оплачено. Товар выдан</p>
                </div>
            {else}
                <div class="bs-callout bs-callout-danger mt-10">
                    <p>Авторизуйтесь, чтобы приобрести товар</p>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="right_block">
    {categories}

    {if(is_auth())}
        {include file="/home/right_col.tpl"}
    {else}
        {include file="/index/reg_form.tpl"}
    {/if}
</div>

<script>
    setRconShopProductTarif(
         {{$tarifs[0]->id}},
        '{{$tarifs[0]->price}}',
        '{{$tarifs[0]->title}}'
    );
</script>