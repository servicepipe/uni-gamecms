<div class="page">
    <div class="block">
        <div class="block_head">
            Категория
        </div>

        <div class="row">
            <div class="col-md-2">
                <button class="btn btn-default btn-block" data-target="#forbidden-words" data-toggle="modal">Добавить</button>
            </div>
            <div class="col-md-5">
                <select id="category" class="form-control" onchange="loadProducts();">
                    <option value="0">Категорий нет</option>
                </select>
            </div>
            <div class="col-md-5">
                <select id="server" class="form-control" onchange="loadCategoriesOptions(); loadCategories();">
                    {servers}
                </select>

                <script>loadCategoriesOptions();</script>
            </div>
        </div>

        <div id="forbidden-words" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Категории</h4>
                    </div>
                    <div class="modal-body">
                        <div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" onclick="addCategory()">Добавить</button>
							</span>
                            <input type="text" class="form-control" id="category_title" maxlength="255" placeholder="Введите название">
                        </div>

                        <hr>

                        <div id="categories">
                            <script>loadCategories();</script>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="block">
                <div class="block_head">
                    Добавить продукт
                </div>

                <label for="title">Название</label>
                <input type="text" class="form-control" maxlength="255" id="title" placeholder="Введите название">

                <label for="status" class="mt-10">Продажа</label>
                <select class="form-control" id="status">
                    <option value="1">Продается</option>
                    <option value="2">Не продается</option>
                </select>

                <label for="image" class="mt-10">Изображение</label><br>
                <img id="image-preview" src="{image}" alt="Стандартное изображение">
                <input type="file" class="input-file w-100" maxlength="255" id="image" onchange="setImagePreview(this, '#image-preview')">

                <label for="is-has-tarifs" class="mt-10">Наличие тарифов</label>
                <select class="form-control" id="is-has-tarifs" onchange="if($(this).val() == 1) { $('#has-tarifs').fadeIn(0); $('#has-not-tarifs').fadeOut(0) } else { $('#has-tarifs').fadeOut(0); $('#has-not-tarifs').fadeIn(0) }">
                    <option value="2">Не имеет тарифов</option>
                    <option value="1">Имеет тарифы</option>
                </select>

                <div id="has-not-tarifs">
                    <label for="tarif-price" class="mt-10">Цена</label>
                    <input type="text" class="form-control" maxlength="11" id="tarif-price" placeholder="Введите цену">

                    <label for="tarif-command" class="mt-10">Команда</label>
                    <input type="text" class="form-control" maxlength="512" id="tarif-command" placeholder="Введите rcon команду">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="block">
                <div class="block_head">
                    Описание
                </div>

                <textarea id="description" class="form-control maxMinW100" rows="5"></textarea>
            </div>
        </div>
        <div class="col-md-5">
            <div class="block">
                <div class="block_head">
                    Параметры для ввода пользователем
                </div>

                <div class="bs-callout bs-callout-info mb-10">
                    <p>
                        Укажите здесь параметры, которые необходимо будет ввести пользователю при покупке услуги.
                        В поле "Переменная" необходимо ввести то кодовое слово, которое будет фигурировать в поле "Команда",
                        на его место будет подставляться введеное пользователем значение. При вводе переменной {steamid} - введеное
                        пользователем значение будет проверяться на корректно введеный STEAM ID
                    </p>
                </div>

                <input type="hidden" id="command-params-count" value="0">

                <div class="row">
                    <div class="col-md-5">
                        <b>Переменная</b>
                    </div>
                    <div class="col-md-5">
                        <b>Название</b>
                    </div>
                </div>

                <form id="command-params" class="mb-10"></form>

                <button type="button" class="btn btn-default" onclick="addCommandParam();">
                    Добавить
                </button>
            </div>
        </div>
        <div class="col-md-9">
            <div class="block" id="has-tarifs" style="display: none">
                <div class="block_head">
                    Тарифы
                </div>

                <div class="bs-callout bs-callout-info mb-10">
                    <p>
                        При наличии тарифов каждой цене будет соответствовать своя rcon команда,
                        благодаря этому вы можете указать прямо в команде параметр, от которого будет зависеть
                        цена
                    </p>
                </div>

                <input type="hidden" id="tarifs-count" value="0">

                <div class="row">
                    <div class="col-md-2">
                        <b>Цена</b>
                    </div>
                    <div class="col-md-3">
                        <b>Название</b>
                    </div>
                    <div class="col-md-5">
                        <b>Команда</b>
                    </div>
                </div>

                <form id="tarifs" class="mb-10"></form>

                <button type="button" class="btn btn-default" onclick="addTarif();">
                    Добавить
                </button>
            </div>
        </div>
    </div>

    <button class="btn2 btn-lg mt-10" onclick="saveProduct();">Создать</button>
    <br><br>

    <div class="block">
        <div class="block_head">
            Продукты
        </div>

        <div class="table-responsive mb-0">
            <table class="table table-bordered mb-0">
                <thead>
                <tr>
                    <td>
                        Название
                    </td>
                    <td>
                        Продажа
                    </td>
                    <td>
                        Тарифы
                    </td>
                    <td>
                        Действие
                    </td>
                </tr>
                </thead>
                <tbody id="products">
                    <tr>
                        <td colspan="10">Товаров нет</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
	init_tinymce('description', '{{md5($conf->code)}}', 'full');
	addCommandParam();
	addTarif();
</script>