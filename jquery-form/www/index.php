<?php
$title = 'Заполните таблицу';
include_once './templates/head.php';

date_default_timezone_set('Europe/Saratov');
?>

    <header class="bg-primary text-center text-white py-3">
        <h2>Форма для данных</h2>
    </header>

    <div class="container h-100">
        <form class="mx-auto mt-5">


            <div class="form-group d-flex align-items-center">
                <label for="changes-date" class="me-4 d-block">Дата изменений:</label>
                <input
                        type="date"
                        class="form-control"
                        id="changes-date"
                        value="<?= date('Y-m-d'); ?>"
                        required
                />
            </div>


            <div class="form-group d-flex align-items-center">
                <label for="sizes-from">Время замеров:</label>
                <div id="sizes-from" class="d-flex align-items-center">
                    <small class="form-text text-muted mx-2">От</small>
                    <input
                            type="time"
                            class="form-control"
                            value="<?php echo date('H:i'); ?>"
                            required
                    />
                    <small class="form-text text-muted mx-2">До</small>
                    <input
                            type="time"
                            class="form-control"
                            value="<?php echo date('H:i'); ?>"
                            required
                    />
                </div>
            </div>

            <div class="form-group d-flex align-items-center">
                <label for="department" class="me-4 d-block">Отделение:</label>
                <select name="department" id="department" class="form-select" required>
                    <option value="1.1">1.1</option>
                    <option value="1.2">1.2</option>
                    <option value="1.4">1.4</option>
                    <option value="1.5">1.5</option>
                </select>
            </div>


            <div class="row justify-content-between text-center">
                <div class="col-3 items-container">
                    <label for="kapelnica">Капельница</label>

                    <div class="input-group mt-2 mx-auto">
                        <button type="button" class="btn btn-outline-success btn-sm">Добавить</button>
                        <button type="button" class="btn btn-outline-danger btn-sm">Удалить все</button>
                    </div>
                    <ul class="mt-2" id="kapelnica">
                        <li class="">
                            <div class="input-group">
                                <span class="form-control d-inline-block bg-light">Объём:</span>
                                <input type="number" class="form-control" required/>
                            </div>
                            <div class="input-group">
                                <span class="form-control d-inline-block bg-light">ЕС:</span>
                                <input type="number" class="form-control" required/>
                            </div>
                            <div class="input-group">
                                <span class="form-control d-inline-block bg-light">рН:</span>
                                <input type="number" class="form-control" required/>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm w-auto rounded-circle mt-1">&#10006;</button>
                        </li>
                    </ul>
                </div>
                <div class="col-3 border">
                    One of three columns
                </div>
                <div class="col-3 border">
                    One of three columns
                </div>
            </div>


            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
        </form>
    </div>

<?php
include_once './templates/foot.php';
