# coding: utf-8

from flask_security import current_user

from nl import db


__all__ = [
    'Config',
    'UserConfig',
]


class Config(db.Model):
    """
    Place to store settings used for dynamic software configuration.
    """
    __tablename__ = 'configuration'

    key = db.Column(db.String(255), primary_key=True)
    value = db.Column(db.String(255), nullable=False)

    @staticmethod
    def get(key, default=None):
        value = Config.query.filter_by(key=key).first().value
        if value is None:
            return default
        return value

    
class UserConfig(db.Model):
    """
    User profile settings.
    """
    __tablename__ = 'users_configuration'

    key = db.Column(db.String(255), primary_key=True)
    user_id = db.Column(db.ForeignKey('auth_users.id', ondelete='CASCADE', onupdate='CASCADE'),
                        nullable=False, index=True)
    value = db.Column(db.String(255), nullable=False)

    user = db.relationship('User', backref='configs')

    @staticmethod
    def get(key, user=None, default=None):
        if user == None:
            user = current_user.id
        value = UserConfig.query.filter_by(key=key, user=user).first().value
        if value is None:
            return default
        return value
