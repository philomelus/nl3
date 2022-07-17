from flask import Blueprint


bp = Blueprint("customers", __name__, url_prefix="/customers")


from nl.admin.customers import handlers
