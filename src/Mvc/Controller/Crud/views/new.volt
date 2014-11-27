<div class="row widget recruiter-new">
    <div class="col-xs-12">
        <div class="widget widget-default-spacer">
            <div class="spacer spacer30"></div>
        </div>
        <div class="widget widget-page-header">
            <h3>{{ i18n._('Add record') }}</h3>
        </div>
        <div class="widget widget-default-spacer">
            <div class="spacer spacer22"></div>
        </div>
        <div class="widget widget-default-page">
            <div class="row widget">
                <div class="col-xs-12">
                    <div class="widget widget-content">
                        <div class="form-edit">
                            <form action="{{ url.get(['for': router.getMatchedRoute().getName(), 'action': 'create']) }}" method="POST" role="form">
                                {{ partial('./_form', ['form': form]) }}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>