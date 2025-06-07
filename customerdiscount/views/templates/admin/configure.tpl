<form class="form" method="post" enctype="multipart/form-data">
  <div class="panel panel-body">
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
        <label class="control-label">
          Activar modulo
        </label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-4">
          <div class="input-group col-lg-12">
              <div class="col-xs-12 col-sm-12 col-md-7 col-lg-12">
                  <input type="checkbox" name="enable-module"
                         class="form-control"
                         {if $enable_module == 1}checked{/if}>
              </div>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <button type="submit" name="submit-{$moduleName}" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</form>


<div class="col-sm-12 col-md-12 col-lg-8" id="discountform__table">
  <button type="button" class="btn btn-primary discountform__create">
    <i class="icon icon-md icon-plus"></i>&nbsp;&nbsp;Crear
  </button>
  <table class="table" style="margin-top: 10px;">
    <thead>
      <tr>
        <th>ID</th>
        <th>Titulo</th>
        <th>Cliente</th>
        <th>Categoria</th>
        <th>Descuento</th>
        <th>Tipo de descuento</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$allDiscounts item=$discount key=$key}
        <tr data-id="{$discount.id}" data-id-customer="{$discount.id_customer}" data-id-category="{$discount.id_category}" data-title="{$discount.title}" data-discount="{$discount.discount}" data-discount_type="{$discount.discount_type}">
          <td>{$discount.id}</td>
          <td>{$discount.title}</td>
          <td>{$discount.firstname} {$discount.lastname} ({$discount.email})</td>
          <td>{$discount.category}</td>
          <td>{$discount.discount_format}</td>
          <td>
            {if $discount.discount_type == 0}
              Monto
            {else}
              Porcentaje
            {/if}
          </td>
          <td>
            <button type="button" class="btn btn-small btn-default discountform__edit">
              <i class="icon icon-lg icon-pencil"></i>
            </button>
            <button type="button" class="btn btn-small btn-default">
              <i class="icon icon-lg icon-trash"></i>
            </button>
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
</div>

<form class="form hidden" method="post" enctype="multipart/form-data" id="discountform">
  <h2>Crear descuento</h2>
  <div class="panel panel-body">
    <input type="hidden" name="discountform[id_customerdiscount]" id="discountform-id_customerdiscount" value="" />
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
        <label class="control-label required">
          Cliente
        </label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-4">
          <div class="input-group col-lg-12">
              <div class="col-xs-12 col-sm-12 col-md-7 col-lg-12">
                  <select class="form-control" name="discountform[id_customer]" id="discountform-id_customer" required>
                    <option value>Seleccione cliente</option>
                    {foreach from=$customers item=$customer key=$key}
                      <option value="{$customer.id_customer}">{$customer.firstname} {$customer.lastname} ({$customer.email})</option>
                    {/foreach}
                  </select>
              </div>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
        <label class="control-label required">
          Categoria
        </label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-4">
          <div class="input-group col-lg-12">
              <div class="col-xs-12 col-sm-12 col-md-7 col-lg-12">
                  <select class="form-control" name="discountform[id_category]" id="discountform-id_category" required>
                    <option value>Seleccione categoria</option>
                    {foreach from=$categories item=$category key=$key}
                      <option value="{$category.id_category}">{$category.name}</option>
                    {/foreach}
                  </select>
              </div>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
        <label class="control-label required">
          Titulo
        </label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-4">
          <div class="input-group col-lg-12">
              <div class="col-xs-12 col-sm-12 col-md-7 col-lg-12">
                  <input type="text" class="form-control" name="discountform[title]" id="discountform-title" value="" required />
              </div>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
        <label class="control-label required">
          Descuento
        </label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-4">
          <div class="input-group col-lg-12">
              <div class="col-xs-12 col-sm-12 col-md-7 col-lg-12">
                  <input type="text" class="form-control" name="discountform[discount]" id="discountform-discount" value="" required />
              </div>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-3">
        <label class="control-label required">
          Tipo de descuento
        </label>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-4">
          <div class="input-group col-lg-12">
              <div class="col-xs-12 col-sm-12 col-md-7 col-lg-12">
                  <select class="form-control" name="discountform[discount_type]" id="discountform-discount_type" required>
                    <option value="0">Monto</option>
                    <option value="1">Porcentaje</option>
                  </select>
              </div>
          </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="form-group">
      <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
        <button type="button" class="btn btn-default discountform__back"><i class="icon icon-md icon-list"></i> Atras</button>
        <button type="submit" name="submit-discountform" class="btn btn-primary pull-right" style="margin-right: 24px;">Aceptar</button>
      </div>
    </div>
  </div>
</form>