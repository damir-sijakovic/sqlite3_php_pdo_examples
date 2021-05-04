<?php

### SQLITE3 PDO Usage examples by Damir Sijakovic. ###

require_once (__DIR__ . '/config.php');
require_once (__DIR__ . '/functions.php');

if (!file_exists(SQLT_EXMP_DB_FILE))
{
    initDatabase();
    insertUser('alphaBeta', 'alpha@beta.com', '1234');
    insertUser('gammaDelta', 'gamma@delta.com', '1234');
    insertUser('thetaAlpha', 'theta@alpha.com', '1234');
    insertUser('omegaEpsilon', 'omega@epsilon.com', '1234');
    insertUser('alphaDelta', 'alpha@delta.com', '1234');
}


// debugDump( findExact('alphaBeta') );
// debugDump( findLike('alpha') );
// debugDump( getAll() );
// debugDump( getLastRecord() );
// debugDump( deleteById(4) );
// debugDump( updateUserName(1, 'foobar') );




