{% block page_content %}
    {% form_theme form 'widget/fields-block.html.twig' %}
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nouveau</h5>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    {{ form_start(form, {'attr': {'role':'form', 'class': 'form'}}) }}
    <div class="modal-body">
        {{ include('_includes/ajax/response.html.twig') }}
        {{ form_widget(form) }}
    </div>
    <div class="modal-footer">
        {# {{ include('_includes/ajax/loader.html.twig') }} #}
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary btn-ajax">Valider</button>
    </div>
    {{ form_end(form) }}
{% endblock %}

{% block javascripts %}
    <script>
        $(function () {
            init_select2('select');
        });
       
    </script>
{% endblock %}