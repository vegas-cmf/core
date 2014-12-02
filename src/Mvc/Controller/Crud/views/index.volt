<div class="table-wrapper products-table section">
    <div class="row header">
        <div class="col-md-12">
            <h3>{{ i18n._('Records list') }}</h3>
            <div class="btn-group pull-right">
                <a class="btn btn-flat success" href="{{ url.get(['for': router.getMatchedRoute().getName(), 'action':'new']) }}">
                    {{ i18n._('+ New record') }}
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <thead>
                <tr>
                    {% for field in fields %}
                        <th>{{ field }}</th>
                    {% endfor %}
                    <th class="options">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                {% if(page.items) %}
                    {% for item in page.items %}
                        <tr>
                            {% for key, field in fields %}
                                <td>
                                    {{ item.readMapped(key) }}
                                </td>
                            {% endfor %}
                            <td align="right">
                                <a class="btn btn-sm btn-default" href="{{ url.get(['for': router.getMatchedRoute().getName(), 'action': 'show', 'params':item._id]) }}">{{ i18n._("Show")}}</a>
                                <a class="btn btn-sm btn-default" href="{{ url.get(['for': router.getMatchedRoute().getName(), 'action': 'edit', 'params':item._id]) }}">{{ i18n._("Update")}}</a>
                                <a class="btn btn-sm confirm-delete" href="{{ url.get(['for': router.getMatchedRoute().getName(), 'action':'delete', 'params':item._id]) }}">{{ i18n._("Delete")}}</a>
                            </td>
                        </tr>
                    {% endfor %}
                {% else %}
                    <tr>
                        <td colspan="{{ fields|length + 1 }}" align="center">{{ i18n._("No records found.")}}</td>
                    </tr>
                {% endif %}
                </tbody>
            </table>
            {{ pagination(page) }}
        </div>
    </div>
</div>
