
from flask import Blueprint

from nl.customers import billing, payments, reports


bp = Blueprint('customers', __name__, url_prefix='/customers')
bp.register_blueprint(billing.bp)
bp.register_blueprint(payments.bp)
bp.register_blueprint(reports.bp)

from nl.customers import handlers

