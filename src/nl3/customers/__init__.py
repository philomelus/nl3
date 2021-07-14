
from flask import Blueprint

from nl3.auth import login_required
from nl3.customers import (billing, payments, reports)


bp = Blueprint('customers', __name__, url_prefix='/customers')

bp.register_blueprint(billing.bp)
bp.register_blueprint(payments.bp)
bp.register_blueprint(reports.bp)


@login_required
@bp.route('/search', methods=('GET', 'POST'))
def search():
    pass


@login_required
@bp.route('/flagstops', methods=('GET', 'POST'))
def flagstops():
    pass


@login_required
@bp.route('/addnew', methods=('GET', 'POST'))
def addnew():
    pass


@login_required
@bp.route('/combined', methods=('GET', 'POST'))
def combined():
    pass

