{% block page_content %}
    <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Détails <?= $entity_class_name ?></h4>
    </div>
   
    <div class="modal-body">
        <table class="table table-bordered">
        <tbody>
            <?php foreach ($entity_fields as $field): ?>
                <tr>
                    <th><?= ucfirst($field['fieldName']) ?></th>
                    <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="modal-footer">
        
        <button type="button" class="btn dark btn-outline" data-bs-dismiss="modal">Fermer</button>
    </div>

{% endblock %}
{% block javascripts %}
    <script>
        
    </script>
{% endblock %}