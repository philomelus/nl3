
from flask import Blueprint


bp = Blueprint('payments', __name__)


from nl.api.customers.payments import handlers

