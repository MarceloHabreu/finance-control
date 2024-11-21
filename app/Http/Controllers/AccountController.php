<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    // Visualizar contas
    public function index()
    {
        $accounts = Account::orderByDesc('created_at')->paginate(3);

        return view('accounts.index', [
            'accounts' => $accounts,
        ]);
    }

    // Detalhar uma conta
    public function show(Account $account)
    {

        return view('accounts.show', ['account' => $account]);
    }

    // Carregar form cadastrar nova conta
    public function create()
    {
        return view('accounts.create');
    }

    // Armazenar nova conta
    public function store(AccountRequest $request)
    {
        try {
            // cadastrando no banco de dados
            $account = Account::create([
                'name' => $request->name,
                'value' => str_replace(',', '.', str_replace('.', '', $request->value)),
                'due_date' => $request->due_date,
            ]);
            // Salvar Log
            Log::info('Conta salva com sucesso', ['id' => $account->id, 'account' => $account]);

            // redirecionar para view show
            return redirect()->route('account.show', ['account' => $account->id])->with('success', 'Conta cadastrada com sucesso!');
        } catch (Exception $e) {

            // Salvar Log
            Log::warning('Conta não cadastrada', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Erro ao cadastrar a conta!');
        }
    }

    // Carregar form de editar conta
    public function edit(Account $account)
    {
        return view('accounts.edit', ['account' => $account]);
    }

    // atualizar dados da nova conta
    public function update(AccountRequest $request, Account $account)
    {

        // editando as info no banco de dados
        try {
            $account->update([
                'name' => $request->name,
                'value' => str_replace(',', '.', str_replace('.', '', $request->value)),
                'due_date' => $request->due_date,
            ]);

            // Salvar Log
            Log::info('Conta editado com sucesso', ['id' => $account->id, 'account' => $account]);

            // redirecionando após atualização
            return redirect()->route('account.show', ['account' => $account->id])->with('success', 'Conta editada com succeso!');
        } catch (Exception $e) {

            // Salvar Log
            Log::warning('Conta não editada', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Erro ao editar a conta!');
        }
    }

    // apagar uma conta
    public function destroy(Account $account)
    {
        // excluir registro do banco de dados
        $deleted = $account->delete();

        if ($deleted) {
            return redirect()->route('account.index')->with('success', 'Conta apagada com succeso!');
        }
        return redirect()->route('account.index')->with('error', 'Erro ao editar a conta!');
    }
}