{% for element in form %}
    {% do element.setAttribute('class', element.getAttribute('class')~' form-control') %}
    {% set hasErrors = form.hasMessagesFor(element.getName()) %}

    <div class="form-group{% if hasErrors %} has-error{% endif %}">
        {% if element.getLabel() %}
            <label for="{{ element.getName() }}">{{ element.getLabel() }}</label>
        {% endif %}
        {% if hasErrors %}
            <span class="help-block">
                {% for error in form.getMessagesFor(element.getName()) %}
                    {{ error }}
                {% endfor %}
            </span>
        {% endif %}
        {{ element }}
    </div>
{% endfor %}

<div class="form-group">
    <button type="submit" class="btn btn-flat success">{{ i18n._('Save') }}</button>
    <a href="{{ url.get(['for': router.getMatchedRoute().getName(), 'action':'index']) }}" class="btn pull-right">{{ i18n._('Cancel') }}</a>
</div>