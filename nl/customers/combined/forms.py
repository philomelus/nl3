
from flask_wtf import FlaskForm
from wtforms import SubmitField


__all__ = ['CombinedForm']


class CombinedForm(FlaskForm):
    refresh = SubmitField('Refresh')
    add = SubmitField()

