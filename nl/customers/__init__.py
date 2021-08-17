
from flask import Blueprint

from nl.customers import billing, combined, payments, popups, reports


bp = Blueprint('customers', __name__, url_prefix='/customers')
bp.register_blueprint(billing.bp)
bp.register_blueprint(combined.bp)
bp.register_blueprint(payments.bp)
bp.register_blueprint(popups.bp)
bp.register_blueprint(reports.bp)

from nl.customers import handlers

