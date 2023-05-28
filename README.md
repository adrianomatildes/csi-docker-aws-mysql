# Pipeline: AppPHPFPM

## Introdução:

A pipeline "AppPHPFPM" é um conjunto de passos automatizados para implantar um aplicativo PHP em um servidor EC2 na AWS, utilizando o serviço de cluster ECS.

## Objetivo:

O objetivo desta pipeline é automatizar o processo de implantação do aplicativo PHP em um ambiente de servidor EC2 na AWS, garantindo a transferência correta de arquivos, configuração do ambiente, execução do aplicativo, criação de um cluster ECS, Load Balancer e Target Group.

## Ações:

1. **Gerar novas chaves SSH**:
   ```shell
   ssh-keygen -t rsa -b 4096 -C "user@host" -q -N ""
   ```

2. **Atualizar o arquivo authorized_keys no host remoto**:
   ```shell
   ssh-copy-id -i ~/.ssh/id_rsa.pub user@host
   ```

3. **Obter a chave conhecida do host remoto**:
   - Execute no terminal do host remoto:
     ```shell
     ssh-keyscan host
     ```
   - Copie o resultado retornado.

4. **Atualizar variável no GitHub**:
   - Cole o resultado do comando `ssh-keyscan` em uma variável na configuração do GitHub, chamada `SSH_KNOWN_HOSTS`.

5. **Checkout**:
   ```shell
   uses: actions/checkout@v2
   ```

6. **Configure AWS credentials**:
   ```shell
   uses: aws-actions/configure-aws-credentials@v1
   with:
     aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
     aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
     aws-region: ${{ secrets.AWS_REGION }}
   ```

7. **Create SSH key**:
   ```shell
   run: |
     mkdir -p ~/.ssh/
     echo "$SSH_PRIVATE_KEY" > ~/.ssh/private_key
     chmod 600 ~/.ssh/private_key
     echo "$SSH_KNOWN_HOSTS" > ~/.ssh/known_hosts
     remote_host=$(echo "$SSH_KNOWN_HOSTS" | cut -d' ' -f1)  # Extrai o nome da máquina remota do arquivo known_hosts
   shell: bash
   env:
     SSH_PRIVATE_KEY: ${{secrets.SSH_PRIVATE_KEY}}
     SSH_KNOWN_HOSTS: ${{secrets.SSH_KNOWN_HOSTS}}
   ```

8. **Transfer files to EC2 instance**:
   ```shell
   run: |
     scp -o StrictHostKeyChecking=no -i ~/.ssh/private_key -r ./* "$remote_host:/home/ec2-user/"
   shell: bash
   ```

9. **Install Docker Compose on EC2 instance**:
   ```shell
   run: |
     ssh -o StrictHostKeyChecking=no -i ~/.ssh/private_key "$remote_host" "curl -L 'https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)' -o docker-compose && chmod +x docker-compose && sudo mv docker-compose /usr/local/bin/"
   shell: bash
   ```

10. **Create Cluster ECS**:
    - Crie um cluster ECS na AWS para hospedar e orquestrar os contêineres do aplicativo.
    - Configure os parâmetros necessários para o cluster, como

 o tipo de instância, tamanho e configuração.

11. **Execute App**:
    ```shell
    run: |
      ssh -o StrictHostKeyChecking=no -i ~/.ssh/private_key "$remote_host" "cd /home/ec2-user && docker-compose up -d"
    shell: bash

12. **Create Load Balancer and Target Group**:
    - Crie um Load Balancer e um Target Group na AWS para distribuir o tráfego entre as instâncias do EC2.
    - Configure os parâmetros necessários para o Load Balancer e o Target Group, como protocolos, portas e instâncias de destino.

## Conclusão:

A pipeline "AppPHPFPM" automatiza o processo de implantação de um aplicativo PHP em um servidor EC2 na AWS. Ela gera novas chaves SSH, atualiza o arquivo authorized_keys no host remoto, realiza o checkout do repositório, configura as credenciais da AWS, cria uma chave SSH, transfere arquivos para a instância do EC2, instala o Docker Compose, cria um cluster ECS, executa o aplicativo e cria um Load Balancer com um Target Group. Antes de executar o push, lembre-se de copiar o resultado do comando `ssh-keyscan` e colar na variável `SSH_KNOWN_HOSTS` na configuração do GitHub.