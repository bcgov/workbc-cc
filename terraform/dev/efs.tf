resource "aws_efs_file_system" "workbc-cc" {
  creation_token                  = "workbc-cc-efs"
  encrypted                       = true

  tags = merge(
    {
      Name = "workbc-cc-efs"
    },
    var.common_tags
  )
}

resource "aws_efs_mount_target" "data_azA" {
  file_system_id  = aws_efs_file_system.workbc-cc.id
  subnet_id       = sort(module.network.aws_subnet_ids.data.ids)[0]
  security_groups = [aws_security_group.efs_security_group.id]
}

resource "aws_efs_mount_target" "data_azB" {
  file_system_id  = aws_efs_file_system.workbc-cc.id
  subnet_id       = sort(module.network.aws_subnet_ids.data.ids)[1]
  security_groups = [aws_security_group.efs_security_group.id]
}


/*
resource "aws_efs_file_system_policy" "policy" {
  file_system_id = aws_efs_file_system.sample-drupal.id

  bypass_policy_lockout_safety_check = true

  policy = <<POLICY
{
    "Version": "2012-10-17",
    "Id": "ExamplePolicy01",
    "Statement": [
        {            
            "Effect": "Allow",
            "Principal": {
                "AWS": "${aws_iam_role.sample_app_container_role.arn}"
            },
            "Resource": "${aws_efs_file_system.sample-drupal.arn}",
            "Action": [
                "elasticfilesystem:ClientMount",
                "elasticfilesystem:ClientWrite",
                "elasticfilesystem:ClientRootAccess"
            ],
            "Condition": {
                "Bool": {
                    "aws:SecureTransport": "true"
                }
            }
        }
    ]
}
POLICY
}

resource "aws_efs_access_point" "sites" {
  file_system_id = aws_efs_file_system.sample-drupal.id
  posix_user {
      uid  = "33"
      gid = "33"
  }
  root_directory {
      creation_info {
          owner_gid   = "33"
          owner_uid   = "33"
          permissions = "0755"
      }
      path = "/sites"
  }
  tags = merge(
    {
        Name        = "sites"
    },
    local.common_tags
  )
}

resource "aws_efs_access_point" "modules" {
  file_system_id = aws_efs_file_system.sample-drupal.id
  posix_user {
      uid  = "33"
      gid = "33"
  }
  root_directory {
      creation_info {
          owner_gid   = "33"
          owner_uid   = "33"
          permissions = "0755"
      }
      path = "/modules"
  }
  tags = merge(
    {
        Name        = "modules"
    },
    local.common_tags
  )
}

resource "aws_efs_access_point" "themes" {
  file_system_id = aws_efs_file_system.sample-drupal.id
  posix_user {
      uid  = "33"
      gid = "33"
  }
  root_directory {
      creation_info {
          owner_gid   = "33"
          owner_uid   = "33"
          permissions = "0755"
      }
      path = "/themes"
  }
  tags = merge(
    {
        Name        = "themes"
    },
    local.common_tags
  )
}

resource "aws_efs_access_point" "profiles" {
  file_system_id = aws_efs_file_system.sample-drupal.id
  posix_user {
      uid  = "33"
      gid = "33"
  }
  root_directory {
      creation_info {
          owner_gid   = "33"
          owner_uid   = "33"
          permissions = "0755"
      }
      path = "/profiles"
  }
  tags = merge(
    {
        Name        = "profiles"
    },
    local.common_tags
  )
}*/
