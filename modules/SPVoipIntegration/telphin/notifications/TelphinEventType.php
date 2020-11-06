<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SPVoipIntegration\telphin\notifications;

/**
 * Description of TelphinEventType
 *
 * @author nikita
 */
class TelphinEventType {
    const DIAL_IN = 'dial-in';
    const DIAL_OUT = 'dial-out';
    const HANGUP = 'hangup';
    const ANSWER = 'answer';
}
