
from flask import Blueprint


bp = Blueprint('customers', __name__)


from nl.api.customers import combined
from nl.api.customers import payments


bp.register_blueprint(combined.bp, url_prefix='/combined')
bp.register_blueprint(payments.bp, url_prefix='/payments')


from nl.api.customers import handlers
