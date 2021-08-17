
from flask import Blueprint


bp = Blueprint('combined', __name__)


from nl.api.customers.combined import handlers

