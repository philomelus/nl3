from flask import Blueprint


bp = Blueprint("payments", __name__, url_prefix="/payments")


from nl.customers.payments import handlers
