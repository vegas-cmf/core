<div class="table-wrapper products-table section">
    <div class="row header">
        <div class="col-md-12">
            <h3>{{ i18n._('Record details') }}</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <tbody>
                {% for key, field in fields %}
                    <th>{{ field }}</th>
                    <td>{{ item.readMapped(key) }}</td>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>
