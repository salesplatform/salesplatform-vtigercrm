<?php
namespace SPVoipIntegration\mcntelecom\notifications;

class MCNEventType {
    const ON_IN_CALLING_START = 'onInCallingStart';
    const ON_IN_CALLING_END = 'onInCallingEnd';
    const ON_OUT_CALLING_START = 'onOutCallingStart';
    const ON_OUT_CALLING_ANSWERED = 'onOutCallingAnswered';
    const ON_OUT_CALLING_END = 'onOutCallingEnd';
    const ON_IN_CALLING_MISSED = 'onInCallingMissed';
    const ON_IN_CALLING_ANSWERED = 'onInCallingAnswered';
    const ON_CLOSE_INCOMING_NOTICE = 'onCloseIncomingNotice';
    const ON_OUT_CALLING_MISSED = 'onOutCallingMissed';
}
