{% extends '_admin/_includes/index.html.twig' %}

{% block title %}Liste des <?= $entity_twig_var_plural ?>{% endblock %}

{% block page_content %}
    <div class="card card-custom mt-4">
        <div class="card-header">
            <div class="col-md-9">
                <h3 class="card-title">Liste</h3>
            </div>
            <div class="col-md-3">
                <div class="float-end">
                    <a href="{{ path('<?= $route_name ?>_new') }}" class="btn btn-primary font-weight-bolder"  
                        data-bs-toggle="modal" data-bs-target="#extralargemodal">>
                        <span class="svg-icon svg-icon-md"><i class="ki ki-solid-plus"></i></span>
                        Nouveau
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!--begin: Search Form-->
            <!--begin::Search Form-->
            <div class="row">
                <div class="col-sm-12">
                    <div id="grid_<?= $route_name ?>" class="grid-dt-wrapper">Chargement....</div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}


{% block javascripts %}
    <script src="{{ asset('assets/js/datatable.js') }}"></script>
    <script> 
        $(function() { 
              $('#grid_<?= $route_name ?>').initDataTables({{ datatable_settings(datatable) }}, {
                  searching: true,
                  ajaxUrl: "{{ path('<?= $route_name ?>_index') }}",
                  language: {
                      url: asset_base_path + "/js/i18n/French.json"
                  }
              });
        });
    </script>
{% endblock %}
