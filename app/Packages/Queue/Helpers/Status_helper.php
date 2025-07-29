<?php

class QueueStatus
{
	const QUEUED = 'queued';
	const RUNNING = 'running';
	const SENT = 'sent';
	const DONE = 'done';
	const FAILED = 'failed';
}

class QueueType
{
	const SMS = 'sms';
	const EMAIL = 'email';
	const PUSH_NOTIFICATION = 'push_notification';
	const FROGSMS = 'frog_sms';
	const FROGMAIL = 'frog_email';
	const TASK = 'task';
}

/* End of Status_helper file */
