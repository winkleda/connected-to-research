            // Try to find all elements that matter to our database
            if(strncmp($line, "<DATE>", strlen("<DATE>")) == 0)
                $date = $mysqli->escape_string(str_replace("<DATE>", "", $line));
            if(strncmp($line, "<YEAR>", strlen("<YEAR>")) == 0)
                $year = $mysqli->escape_string(str_replace("<YEAR>", "", $line));
            if(strncmp($line, "<AGENCY>", strlen("<AGENCY>")) == 0)
                $agency = $mysqli->escape_string(str_replace("<AGENCY>", "", $line));