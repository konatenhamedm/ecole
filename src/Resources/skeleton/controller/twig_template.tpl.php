{% extends '_includes/index.html.twig' %}

{% block title %}
    {{ controller_name }}
{% endblock %}

{% block breadcrumb %}
    <li class="breadcrumb-item">
        <a href="{{path('default')}}" class="text-muted">Tableau de bord</a>
    </li>
    <li class="breadcrumb-item">
        {{ controller_name }}
    </li>
{% endblock %}

{% block style %}
    {{ parent() }}
    <style type="text/css" media="screen">
      
    </style>
{% endblock %}
{% block page_content %}
    <div class="card card-custom">
		<div class="card-header card-header-tabs-line">
            <div class="card-title">
                <h3 class="card-label"></h3>
            </div>
			<div class="card-toolbar">							
			</div>
		</div>
		<div class="card-body">
			
		</div>
	</div>
{% endblock %}

{% block java %}
    <script>
        $(document).ready(function () {
            
        });
    </script>
{% endblock %}