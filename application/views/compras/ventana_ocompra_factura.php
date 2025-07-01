<form method="post">
    <table border="0" align="center" cellpading="5" cellspacing="5">
        <tbody>
            <tr>
                <td>FACTURA</td>
                <td><input type="text" name="numero" id="numero" value="<?php echo $numero_factura;?>" /></td>
            </tr>
            <tr align="center">
                <td colspan="2">
                    <input type="submit" value="Grabar" />&nbsp;
                    <input type="button" value="Cancelar" onclick="window.close();"/>
                    <input type="hidden" name="codigo" id="codigo" value="<?php echo $ocompra;?>" />
                </td>
            </tr>
        </tbody>
    </table>

</form>