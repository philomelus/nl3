from flask import Blueprint


bp = Blueprint("billing", __name__, url_prefix="/billing")


from nl.customers.billing import handlers
