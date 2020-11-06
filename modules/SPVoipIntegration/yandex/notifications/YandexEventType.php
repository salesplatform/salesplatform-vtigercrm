<?php

namespace SPVoipIntegration\yandex\notifications;

class YandexEventType {
    const INCOMING_CALL = 'IncomingCall';
    const INCOMING_CALL_RINGING = 'IncomingCallRinging';
    const INCOMING_CALL_STOP_RINGING = 'IncomingCallStopRinging';
    const INCOMING_CALL_CONNECTED = 'IncomingCallConnected';
    const INCOMING_CALL_COMPLETED = 'IncomingCallCompleted';
    const OUTGOING_CALL = 'OutgoingCall';
    const OUTGOING_CALL_CONNECTED = 'OutgoingCallConnected';
    const OUTGOING_CALL_COMPLETED = 'OutgoingCallCompleted';
    const CALLBACK_CALL = 'CallbackCall';
    const CALLBACK_CALL_RINGING = 'CallbackCallRinging';
    const CALLBACK_CALL_STOP_RINGING = 'CallbackCallStopRinging';
    const CALLBACK_CALL_CONNECTED = 'CallbackCallConnected';
    const CALLBACK_CALL_COMPLETED = 'CallbackCallCompleted';
}
