CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_register`(
IN p_personal_id int(11),
IN p_firstname varchar(50),
IN p_lastname varchar(50),
IN p_salutation varchar(5),
IN p_gender varchar(1),
IN p_comment varchar(500),
OUT p_existing varchar(1)
)
BEGIN
	DECLARE existing varchar(1);
    
    SELECT personal_id INTO existing 
    FROM rccl 
    WHERE personal_id = p_personal_id;
    
    IF (existing != '') THEN
    	SET p_existing = 'Y';
    ELSE 
    	INSERT INTO rccl ( personal_id, firstname, lastname, salutation, gender, comment ) VALUES ( p_personal_id, p_firstname, p_lastname, p_salutation, p_gender, p_comment );
    	SET p_existing = 'N';
    END IF;
END