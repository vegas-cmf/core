<div class="table-wrapper products-table section">
    <div class="row header">
        <div class="col-md-12">
            <h3>Record details</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <tbody>
                {% for key, field in fields %}
                    <tr>
                        <th>{{ field }}</th>
                        <td>{{ record.readMapped(key) }}</td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
</div>
