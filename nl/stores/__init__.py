
from flask import Blueprint


bp = Blueprint('stores', __name__, url_prefix='/stores')


from nl.stores import handlers

